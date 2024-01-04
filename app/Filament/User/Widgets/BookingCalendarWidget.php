<?php

namespace App\Filament\User\Widgets;

use App\Models\Rider;
use App\Models\BusRoute;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class BookingCalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = BusRoute::class;

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return BusRoute::query()
            ->get()
            ->map(
                fn (BusRoute $event) => [
                    'id' => $event->id,
                    'title' => $event->name,
                    'start' => Carbon::now(),
                    'end' => Carbon::now(),
                    'url' => ('/Busstop/edit-profile'),
                    'shouldOpenUrlInNewTab' => false,
                    'allDay'=> false,
                ]
            )
            ->all();
    }

    //Generate the form for the clicked event
    public function getFormSchema(): array
    {
        return [
            Select::make('BusRouteBooking')
            ->label('Bus Route')
            ->options(BusRoute::where('is_active', 1)
                ->with('timeslot') // Assuming the relationship method is named timeslot
                ->get()
                ->mapWithKeys(function ($busRoute) {
                    $timeslot_id = $busRoute->timeslot->id;
                    $departure_time = $busRoute->timeslot->departure_time;
                    return [$busRoute->id => '(' . $departure_time . ') ' . $busRoute->name];
                })
                ->toArray())
            ->required(),
            
            Grid::make('grid')
                ->columns(2)
                ->schema([
                    Select::make('rider')
                        ->label('Rider')
                        ->options(Rider::where('user_id', auth()->user()->id)->get()->pluck('name', 'id')->toArray())
                        ->required(),
                ]),
        ];
    }

     /**
     * Determine where the widget should be displayed
     *
     * @see https://filamentphp.com/docs/3.x/panels/dashboard#conditionally-hiding-widgets
     * @return bool
     */
    public static function canView(): bool
    {
        

        return false;
    }
}
