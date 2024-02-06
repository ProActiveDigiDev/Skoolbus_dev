<?php

namespace App\Filament\Admin\Pages;

use App\Models\Rider;
use App\Models\BusRoute;
use Filament\Pages\Page;
use App\Models\UserBooking;
use App\Models\WebsiteConfigs;
use Illuminate\Support\Carbon;
use App\Models\EmergencyContact;
use Filament\Pages\Actions\Action;
use App\Models\EmergencyInformation;
use App\Http\Controllers\WhatsAppController;
use App\Models\UserProfile;

class RideRequest extends Page
{
    protected static string $view = 'filament.admin.pages.ride-request';

    protected static ?string $title = 'Ride Request';
    

    public array $website_configs = [];
    public string $rider_id = '';
    public ?Rider $rider_info = null;
    public ?UserBooking $current_booking = null;
    public ?UserBooking $next_booking = null;
    public ?BusRoute $route_info = null;
    public ?EmergencyInformation $emergency_info = null;
    public ?EmergencyContact $emergency_contact = null;
    public array $request_message = [];

    public function mount(string $rider)
    {
        if (!auth()->check() || !auth()->user()->hasRole(['super_admin', 'admin_user', 'driver_user'])) {
            return redirect()->route("user-login");
        }        

        $this->website_configs = $this->getWebsiteConfigs();

        $this->rider_id = encID($rider, 'decr');

        $this->emergency_contact = $this->getRiderEmergencyContact($this->rider_id);
        $this->emergency_info = $this->getRiderEmergencyInformation($this->rider_id);

        
        $this->request_message = $this->validateRequestedBooking();
    }

    public function getRiderInfo($rider_id)
    {
        $rider_info = Rider::where('id', $rider_id)
        ->get()
        ->first();

        return $rider_info = $rider_info ? $rider_info : null;
    }

    public function getParentInfo($parent_id)
    {
        $parent_info = UserProfile::where('user_id', $parent_id)
        ->get()
        ->first();

        return $parent_info = $parent_info ? $parent_info : null;
    }

    public function getRiderEmergencyContact($rider_id)
    {
        //get the user id from the rider id
        $user_id = Rider::where('id', $rider_id)->get()->first()->user_id;
        //get the emergency contact info
        $emergency_contact = EmergencyContact::where('user_id', $user_id)->get()->first();
        
        return $emergency_contact = $emergency_contact ? $emergency_contact : null;        
    }

    public function getRiderEmergencyInformation($rider_id)
    {
        //get the user id from the rider id
        $user_id = Rider::where('id', $rider_id)->get()->first()->user_id;
        //get the emergency contact info
        $emergency_info = EmergencyInformation::where('user_id', $user_id)->get()->first();

        return $emergency_info = $emergency_info ? $emergency_info : null;
    }

    public function getNextBooking($rider_id)
    {
        $today = date('Y-m-d');
        $time = Carbon::now('Africa/Johannesburg');

        
        //make current time for dev purposes
        $time = Carbon::createFromFormat('H:i', '13:30', 'Africa/Johannesburg');

        //get all bookings for today
        $riderBookings = UserBooking::where('rider_id', $rider_id)
        ->where('busroute_date', '=', $today)
        ->where('busroute_status', '=', 'booked')
        ->orderBy('busroute_date', 'asc')
        ->with(['busroute.timeslot'])
        ->get();

        $currentBooking = null;
        
        //check if booking is within 45 minutes before departure time and 15 minutes after departure time
        foreach($riderBookings as $booking){
            $departure_time = $booking->busroute->timeslot->departure_time;
            $departure_time = Carbon::createFromFormat('H:i', $departure_time, 'Africa/Johannesburg');
            $futureTime = $departure_time->copy()->addminutes(16)->toTimeString(); //Allow 15 minutes after departure time to pick up rider
            $pastTime = $departure_time->copy()->subminutes(46)->toTimeString(); //Allow 45 minutes before departure time to pick up rider
            if($time->toTimeString() > $pastTime && $time->toTimeString() < $futureTime){
                $currentBooking = $booking;
            }
        }
        
        if($riderBookings && $riderBookings->count() > 0){
            //remove the $currentBooking if there is one
            if($currentBooking){
                $riderBookings = $riderBookings->where('id', '!=', $currentBooking->id);
            }
            //remove the bookings that are in the past
            $riderBookings = $riderBookings->where('busroute.timeslot.departure_time', '>', $time->toTimeString());
            //sort the collection by departure time
            $riderBookings = $riderBookings->sortBy('busroute.timeslot.departure_time');
            //get the first booking in the collection
            $closestNextBookingAfter = $riderBookings->first();
            //set the booking after the closest next booking
            $this->next_booking = $closestNextBookingAfter ?? null;
        }


        return $currentBooking = $currentBooking ?? null;
    }

    public function isBookingStatus($booking)
    {
        //check that booking busroute_status is booked
        if($booking['busroute_status'] != 'booked'){
           return $booking['busroute_status'];
        }else{
            return 'booked';
        }

    }

    public function getBookingRouteInfo($route_id)
    {
        //get bus route info with location and timeslot info
        $route_info = BusRoute::where('id', $route_id)
        ->with('timeslot')
        ->with('fromLocation')
        ->with('toLocation')
        ->get()
        ->first();

        return $route_info = $route_info ? $route_info : null;
    }

    public function acceptRequest()
    {
        $route_info = $this->getBookingRouteInfo($this->current_booking['busroute_id']);
        $parent_info = $this->getParentInfo($this->rider_info->user_id);
        $to_num = $parent_info->phone;
        $now = Carbon::now('Africa/Johannesburg')->format('H:i');

        $message = $this->rider_info->name . " has been picked up by the Skoolbus at " .  $route_info->fromLocation->name . ' (' . $now . "). Heading to ". $route_info->toLocation->name . ".";

        $bookingStatus = $this->updateBookingStatus('intransit');
        
        if($bookingStatus){
            $this->addRiderToCurrentRoute();        
            sendWhatsAppNotification($to_num, $message);
        }
        $this->request_message = ['success', 'DONE'];

        return $bookingStatus;

    }

    public function rejectRequest()
    {
        //change booking status to cancelled
        $bookingStatus = $this->updateBookingStatus('cancelled');
        if($bookingStatus){
            $this->request_message = ['success', 'CANCELLED'];
        }
       
        return $bookingStatus;
    }

    public function updateBookingStatus($status)
    {
        //change booking status to intransit
        $this->current_booking['busroute_status'] = $status;

        switch($status){
            case 'intransit':
                //change busroute_pickup to true
                $this->current_booking['busroute_pickup'] = true;
                $this->current_booking['busroute_driver'] = auth()->user()->id;
                break;
            case 'completed':
                //change busroute_dropoff to false
                $this->current_booking['busroute_dropoff'] = true;
                //change busroute_status to '
                break;
            case 'cancelled':
                //change busroute_pickup to false
                $this->current_booking['busroute_pickup'] = false;
                //change busroute_dropoff to false
                $this->current_booking['busroute_dropoff'] = false;
                break;
        }

        //save the changes
        $return = $this->current_booking->save();

        return $return;
    }

    public function addRiderToCurrentRoute()
    {

    }    

    public function validateRequestedBooking()
    {
        $result = ['success', 'success'];

        if(!$this->rider_info = $this->getRiderInfo($this->rider_id)){
            return ['noRider', 'Rider not found'];
        }
   
        if(!$this->current_booking = $this->getNextBooking($this->rider_id)){
            if($this->next_booking){
                $nextBookingOpeningTime = Carbon::createFromFormat('H:i', $this->next_booking['busroute']['timeslot']['departure_time'], 'Africa/Johannesburg')->subMinutes(45)->format('H:i');
                return ['noBooking', '<sub>Next booked ride:</sub> <br><strong>' . $this->next_booking['busroute']['name'] . '<br>' . $this->next_booking['busroute']['timeslot']['departure_time'] . '<br> <sup>Opening time:<br>' . $nextBookingOpeningTime . '</sup></strong>'];
            }else{
                return ['noBooking', 'No booking found'];
            }
        }

        if(!$this->route_info = $this->getBookingRouteInfo($this->current_booking['busroute_id'])){
            return ['noRoute', 'Route not found'];
        }

        $bookingStatus = $this->isBookingStatus($this->current_booking);
        if($bookingStatus != 'booked'){
            return ['bookingStatus', "This requested booking status is: '" . $bookingStatus . "'"];
        }

        return $result;
    }

    public function getWebsiteConfigs()
    {
        $websiteConfigs = WebsiteConfigs::get()
        ->pluck('var_value', 'var_name')
        ->toArray();
        return $websiteConfigs;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
