<?php

namespace App\Filament\User\Widgets;

use App\Models\Rider;
use Filament\Forms\Get;
use App\Models\BusRoute;
use App\Models\Location;
use App\Models\User;
use App\Models\UserAccount;
use Filament\Forms\Form;
use App\Models\UserBooking;
use App\Models\WebsiteConfigs;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Checkbox;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Bus;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Widgets\Concerns\InteractsWithEvents;

class BookingCalendarWidget extends FullCalendarWidget
{
    use InteractsWithEvents;
    
    public Model | string | null $model = UserBooking::class;

    public $brandColor = WebsiteConfigs::class;

    public array $riderList = [];
    public string $selectedDate;
    public string $selectedDateEnd;
    public string $selectedRoute = '';
    public string $selectedRider = '';
    public bool $isDuplicate = false;
    public bool $isShowAll = false;
    public int $lightnessIncrement = 5;

    public function __construct()
    {
        $this->selectedDate = Carbon::now()->addDay()->format('Y-m-d');
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return UserBooking::query()
        ->with('rider')
        ->where('user_id', auth()->user()->id)
        ->get()
        ->map(
            function (UserBooking $event, $brandColor)
            {
                $brandColor = $this->brandColor::where('var_name', 'site_brand_color_primary')->first()->var_value;
                $this->getRidersColors($event->rider->id, $brandColor, $this->lightnessIncrement);
                $color = $this->riderList[$event->rider->id];
                
                return [
                'id' => $event->id,
                'title' => $event->rider->name,
                'start' => $event->busroute_date,
                'end' => $event->busroute_date,
                'allDay'=> true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'displayEventTime' => true,
                'timeFormat' => null,
                ];
        }
                
        )
        ->all();
    }


    //Generate the form for the clicked event
    public function getFormSchema(): array
    {
        //get show_all input state
        $this->isShowAll = $this->isShowAll ? '' : 'parent';

        //return form schema
        return [
            Grid::make('grid')
                ->columns(7)
                ->schema([
                    Select::make('busroute_id')
                    ->label('Bus Route')
                    ->live()
                    ->options(
                        $this->populateSelect($this->selectedDate, $this->isShowAll ? '' : 'parent')
                        ->mapWithKeys(function ($busRoute) {
                            $departure_time = $busRoute->timeslot->departure_time;
                            return [$busRoute->id => '(' . $departure_time . ') ' . $busRoute->name . ' [Credits: ' . $busRoute->credits_per_ride . ']'];
                        })
                        ->toArray() 
                    )
                    ->required()
                    ->afterStateUpdated(function (?string $state, ?string $old) {
                        $this->selectedRoute = $state;
                        if($this->selectedRider){
                            $this->isDuplicate = $this->riderDuplicateBooking($this->selectedRider, $this->selectedDate, $state);
                        }
                    })
                    ->columnSpan(6),

                    Toggle::make('show_all')
                        ->label('Show All')
                        ->inline(false)
                        ->live()
                        ->afterStateUpdated(function (?string $state, ?string $old) {
                            $this->isShowAll = $state;
                        })
                        ->columnSpan(1),
                ]),
            
            Grid::make('grid')
                ->columns(7)
                ->schema([
                    Select::make('rider_id')
                        ->label('Rider')
                        ->live()
                        ->options(Rider::where('user_id', auth()->user()->id)->get()->pluck('name', 'id')->toArray())
                        ->required()
                        ->afterStateUpdated(function (?string $state, ?string $old) {
                            $this->selectedRider = $state;
                            if($this->selectedRoute){
                                $this->isDuplicate = $this->riderDuplicateBooking($state, $this->selectedDate, $this->selectedRoute);
                            }
                        })
                        ->rules([
                            function () {
                                return function ($attribute, $value, $fail) {
                                    if ($this->isDuplicate === true) {
                                        $fail('You have already booked this route for this Rider.');
                                    }
                                };
                            },
                        ])
                        ->columnSpan(3),

                    Grid::make('grid')
                        ->columns(8)
                        ->columnSpan(4)
                        ->schema([
                            DatePicker::make('busroute_date')
                                ->label('Booking Date')
                                ->live()
                                ->afterStateUpdated(function ($state) {
                                    $this->selectedDate = $state;
                                })
                                ->columnSpan(6),

                            DatePicker::make('repeat_until')
                                ->label('Repeat Until')
                                ->hidden(fn (Get $get) => !$get('repeat'))
                                ->required(fn (Get $get) => $get('repeat'))
                                ->afterStateUpdated(function ($state) {
                                    $this->selectedDateEnd = $state;
                                })
                                ->columnSpan(6),

                            Checkbox::make('repeat')
                                ->label('Repeat')
                                ->live()
                                ->afterStateUpdated(function ($state) {
                                    //change repeat_untill to busroute_date if repeat is false
                                    if(!$state){
                                        $this->selectedDateEnd = $this->selectedDate;
                                    }
                                })
                                ->columnSpan(2),
                        ]),   
                ]),
        ];
    }


    /*Filament and Fullcalendar functions*/
    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label('Make a Booking')
                ->size(ActionSize::Small)
                ->modalHeading('Booke a route')
                ->modalDescription(function(){
                    return 'Available Credits: ' . auth()->user()->user_account->user_credits . '';
                })
                ->modalSubmitActionLabel('Book route')
                ->mountUsing(
                    function (Form $form) {
                        $start = $this->selectedDate;
                        $end = $this->selectedDateEnd;

                        $form->fill([
                            'busroute_date' => $start,
                            'repeat_until' => $end,
                            'repeat' => $start === $end ? false : true,
                        ]);
                    }
                )
                ->beforeFormFilled(function (CreateAction $action) {
                    $start = $this->selectedDate;
                    $end = $this->selectedDateEnd;

                    //check if start and end date are the same then single day is selected
                    if(!$this->isRange($start, $end)){
                        //if no routes available then cancel the action
                        if(empty($this->isDateAvailable($start))){
                            Notification::make()
                                ->danger()
                                ->title('No routes available for ' . $start)
                                ->send();
                        
                            $action->cancel();
                        }
                    }
                })
                ->mutateFormDataUsing(function (array $data): array {
                    //check if repeat is selected
                    if($data['repeat']){
                        //if repeat is selected then set repeat_until to $this->selectedDateEnd
                        $this->selectedDateEnd = $data['repeat_until'];
                    }
                     
                    return [
                        ...$data,
                        'user_id' => auth()->user()->id,
                        'busroute_status' => 'booked',
                        'busroute_credit' => BusRoute::where('id', $data['busroute_id'])->pluck('credits_per_ride')->first(),
                    ];
                })
                ->using(function (array $data, string $model) {
                    $rideCredits = $data['busroute_credit'];
                    $repeatBookingList = $this->getBookingList($data);
                    $rideCredits = $this->getRideCreditTotal($repeatBookingList, $rideCredits);

                    //check if user has enough credits to make booking
                    $canBook = $this->checkUserCredits($rideCredits);
                    
                    if($canBook){
                       if($this->makeBookings($repeatBookingList, $model)){
                            UserAccount::where('user_id', auth()->user()->id)->decrement('user_credits', $rideCredits);
                       }else{
                            Notification::make()
                                ->danger()
                                ->title('An unknown error occured. Please try again.')
                                ->send();
                       }
                    }else{
                        Notification::make()
                            ->danger()
                            ->title('Insufficient credits for this booking: ' . $rideCredits . ' credits required')
                            ->send();
                    }
                })
                ->successNotificationTitle('Booking Made')     
        ];
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view): void
    {
        // Calculate the timezone offset if needed
        [$start, $end] = $this->calculateTimezoneOffset($start, $end, $allDay);

        // Set the selected date(s) variables for form if Range selection
        $this->setFormVariablesForRange($start, $end);

        // You can also call the parent method if necessary
        parent::onDateSelect($start, $end, $allDay, $view);
    }


    /*Helper functions*/
    public function populateSelect($selectedDate, $query = '')
    {
        $allRoutes = $this->getBusRoutes($query);
        $routes = $this->busrouteDayAvailableList($allRoutes, $selectedDate);
        foreach ($routes as $route) {
            $this->busrouteSpaceAvailable($route, $this->selectedDate);
        }
        
        return $routes;
    }

    //Set the color for each rider
    public function getRidersColors($rider_id, $color, $lightnessIncrement)
    {
        // Check if rider_id is in $this->riderList, and if not add it
        if (!array_key_exists($rider_id, $this->riderList)) {
            if (empty($this->riderList)) {
                $this->riderList[$rider_id] = $color; // First rider retains the provided color
            } else {
                // Convert the color to HSL
                list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
                list($h, $s, $l) = $this->rgbToHsl($r, $g, $b);

                // Adjusting the lightness for subsequent riders
                $l = ($l + $lightnessIncrement) % 100;

                // Convert HSL back to hexadecimal color representation
                $newRgb = $this->hslToRgb($h, $s, $l);
                $newColor = sprintf("#%02x%02x%02x", ...$newRgb);

                $this->riderList[$rider_id] = $newColor;
            }
            // Increment the lightness for subsequent riders
            $this->lightnessIncrement = $this->lightnessIncrement + 5;
        }
    }

    private function rgbToHsl($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $l = ($max + $min) / 2;

        if ($max === $min) {
            $h = $s = 0; // achromatic
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            
            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;
                    break;
            }

            $h /= 6;
        }

        return [$h * 360, $s * 100, $l * 100];
    }

    private function hslToRgb($h, $s, $l)
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ($s === 0) {
            $r = $g = $b = $l * 255;
        } else {
            $hTemp2 = function ($temp1, $temp2, $tempH) {
                if ($tempH < 0) {
                    $tempH += 1;
                }
                if ($tempH > 1) {
                    $tempH -= 1;
                }
                if ((6 * $tempH) < 1) {
                    return $temp1 + ($temp2 - $temp1) * 6 * $tempH;
                }
                if ((2 * $tempH) < 1) {
                    return $temp2;
                }
                if ((3 * $tempH) < 2) {
                    return $temp1 + ($temp2 - $temp1) * ((2 / 3) - $tempH) * 6;
                }
                return $temp1;
            };

            $temp2 = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $temp1 = 2 * $l - $temp2;

            $r = round(255 * $hTemp2($temp1, $temp2, $h + (1 / 3)));
            $g = round(255 * $hTemp2($temp1, $temp2, $h));
            $b = round(255 * $hTemp2($temp1, $temp2, $h - (1 / 3)));
        }

        return [(int)$r, (int)$g, (int)$b];
    }

    //get all bus routes according to arguments
    public function getBusRoutes($type = null, $name = null)
    {
        $query = BusRoute::where('is_active', 1);

        switch ($type) {
            case 'school':
                // Return bus routes linked to the school
                $school = $name;

                // Find bus routes based on school id
                $query->where(function ($query) use ($school) {
                    $query->where('from_location_id', $school)
                            ->orWhere('to_location_id', $school);
                });
                break;

            case 'timeslot':
                // Get the current authenticated rider by ID          
                $timeslot = $name;
                // Find bus routes based on timeslot
                $query->where(function ($query) use ($timeslot) {
                    $query->where('timeslot_id', $timeslot);
                });
                break;

            case 'rider':
                // Get the current authenticated rider by ID
                $currentRider = Rider::find($name); // Replace Rider with your actual Rider model name
                if ($currentRider) {            
                    // Extract schools from the linked rider
                    $school = $currentRider->school;
            
                    // Find bus routes based on schools
                    $query->where(function ($query) use ($school) {
                        $query->where('from_location_id', $school)
                                ->orWhere('to_location_id', $school);
                    });
                }
                break;
                
            case 'parent':
                // Fetch the current authenticated user
                $currentUser = auth()->user();
    
                // Retrieve the current user's linked riders
                $linkedRiders = $currentUser->rider_profile;
    
                // Extract schools from the linked riders
                $schools = $linkedRiders->pluck('school')->unique();
    
                // Find bus routes based on schools
                $query->where(function ($query) use ($schools) {
                    $query->whereIn('from_location_id', $schools)
                            ->orWhereIn('to_location_id', $schools);
                });
                break;

            case 'busroute_id':
                // Find bus routes based on busroute_id
                $query->where(function ($query) use ($name) {
                    $query->where('id', $name);
                });
                break;

            default:
                // Return all bus routes if $type is null or not recognized
                break;
        }

        return $query->get();
    }

    //get all bus routes that is available on the selected day of the week
    public function busrouteDayAvailable($route, $selectedDate)
    {
        $dayOfWeek = strtolower(date('l', strtotime($selectedDate))); // Get the day of the week in lowercase
        $isActiveDay = false;
        // Convert JSON string to PHP array
        $daysActive = $route->days_active;

        // Check if the selected day is in the days_active array
        if (in_array($dayOfWeek, $daysActive)) {
            $isActiveDay = true;
        }

        return $isActiveDay;
    }

    //get all bus routes that is available on the selected day of the week
    public function busrouteDayAvailableList($routes, $selectedDate)
    {
        $dayOfWeek = strtolower(date('l', strtotime($selectedDate))); // Get the day of the week in lowercase

        $filteredRoutes = collect($routes)->filter(function ($route) use ($dayOfWeek) {
            $daysActive = $route->days_active; // Convert JSON string to PHP array

            return in_array($dayOfWeek, $daysActive);
        });

        return $filteredRoutes;
    }

    //check if the bus route still has places open on the selected date
    public function busrouteSpaceAvailable($route, $selectedDate)
    {
        $selectedDate = $this->selectedDate;

        $bookings = UserBooking::where('busroute_id', $route->id)
            ->where('busroute_date', $selectedDate)
            ->count();

        $maxRiders = $route->max_riders;

        return $bookings < $maxRiders;
    }

    //check if rider has already booked on the selected date and route
    public function riderDuplicateBooking($rider, $selectedDate, $busroute_id)
    {
        $bookings = UserBooking::where('rider_id', $rider)
            ->where('busroute_date', $selectedDate,)
            ->where('busroute_id', $busroute_id)
            ->get()
            ->count();

        return $bookings > 0;
    }

    //check if date is single or range
    public function isRange($start, $end)
    {
        $start = Carbon::parse($start)->format('Y-m-d');
        $end = Carbon::parse($end)->format('Y-m-d');
        if($start === $end){
            return false;
        }else if($start < $end){
            return true;
        }
    }

    //check if specific date is available
    public function isDateAvailable($date)
    {
        $available = $this->populateSelect($date, $this->isShowAll ? '' : 'parent')->toArray();
        return !empty($available);
    }

    //set selected date(s) variables for form if Range selection
    public function setFormVariablesForRange($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
       
        //check if this is a range and check if there are any routes available for each day in the range
        if($this->isRange($start, $end)){ 

            while ($start->lte($end)) {
                $available = $this->isDateAvailable($start);

                //if no route not available for a day then make notification and set availableArr to false
                if(empty($available)){
                    $availableArr[$start->format('Y-m-d')] = false;

                    Notification::make()
                    ->danger()
                    ->title('No routes available for ' . $start->format('Y-m-d'))
                    ->send();

                }else{
                    $availableArr[$start->format('Y-m-d')] = true;
                }
                $start->addDay();
            }
            //set selected date to first date in $availableArr not false
            $this->selectedDate = array_search(true, $availableArr);

            //set $this->selectedDateEnd to last date in $availableArr not false
            $this->selectedDateEnd = array_search(true, array_reverse($availableArr, true));  
        }else{
            $this->selectedDate = $start->format('Y-m-d');
            $this->selectedDateEnd = $start->format('Y-m-d');
        }
    }

    //create booking for each day in the repeat range
    public function getBookingList($data)
    {
        $test = [];
        $checks = [];
        $start = Carbon::parse($this->selectedDate);
        $end = Carbon::parse($this->selectedDateEnd);
        while ($start->lte($end)) {
            $date = $start->format('Y-m-d');
            $data['busroute_date'] = $date;
            $route = $this->getBusRoutes('busroute_id', $data['busroute_id']);
            $check1 = $this->busrouteDayAvailable($route[0], $date);
            $check2 = $this->busrouteSpaceAvailable($route[0], $date);
            $check3 = $this->riderDuplicateBooking($data['rider_id'], $date, $data['busroute_id']);
            $check4 = $this->isDateAvailable($date);

            $test[$date] = [$check1, $check2, $check3, $check4];
            if($check1 && $check2 && !$check3 && $check4){
                $checks[$data['busroute_date']] = [true, $data];
            }else{
                $checks[$date] = [false];
            }

            $start->addDay();
        }
        return $checks;
    }

    //calculate the total credits for the rides being booked
    public function getRideCreditTotal($data, $RideCredits)
    {
        $totalCredits = 0;
        foreach ($data as $key => $value) {
            if($value[0]){
                $totalCredits += $RideCredits;
            }
        }
        return $totalCredits;
    }

    //check user credits and deduct credits if true
    public function checkUserCredits($totalCredits)
    {
        $userCredits = auth()->user()->user_account->user_credits;
        $canBook = $userCredits >= $totalCredits ? true : false;
        if($canBook){
            return true;
        }else{
            return false;
        }
    }

    //make bookings
    public function makeBookings($repeatBookingList, $model)
    {
        foreach ($repeatBookingList as $key => $value) {
            if($value[0]){
                //get model data
                $data = $value[1];
                //create booking
                $model::create($data);
            }
        }
        return true;
    }

     /**
     * Determine where the widget should be displayed
     * if just false it won't display on dashboard
     *
     * @return bool
     */
    public static function canView(): bool
    {
        return false;
    }
    
}
