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
        $rider_info = Rider::where('id', $rider_id)->get()->first();

        return $rider_info = $rider_info ? $rider_info : null;
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
        $time = Carbon::createFromFormat('H:i', '06:30', 'Africa/Johannesburg');

        //get all bookings for today
        $riderBookings = UserBooking::where('rider_id', $rider_id)
        ->where('busroute_date', '=', $today)
        ->orderBy('busroute_date', 'asc')
        ->with(['busroute.timeslot'])
        ->get();
        
        
        //check if time is in the past and remove from $riderBookings
        $nTime = $time->copy()->subminutes(35)->toTimeString();
        foreach($riderBookings as $booking){
            if($booking['busroute']['timeslot']['departure_time'] < $nTime){
                $riderBookings = $riderBookings->except($booking['id']);
            }
        }
        
        //get the next booking closests to current time
        $currentBooking = null;
        $closestNextBookingTime = null;
        
        foreach($riderBookings as $booking){
            $bookingTime = Carbon::createFromFormat('H:i', $booking['busroute']['timeslot']['departure_time'], 'Africa/Johannesburg');
            
            if ($closestNextBookingTime == null || $bookingTime->diffInSeconds($time) < $closestNextBookingTime->diffInSeconds($time)) {
                $closestNextBookingTime = $bookingTime;
                $currentBooking = $booking;
            }
        }
        
        if($riderBookings->count() > 0){
            //remove the closest next booking from the collection
            $riderBookings = $riderBookings->except($currentBooking['id']);
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
        $bookingStatus = $this->updateBookingStatus('intransit');
        
        if($bookingStatus){
            $this->addRiderToCurrentRoute();        
            $this->sendNotification();
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

    Public function sendNotification()
    {


    }

    public function validateRequestedBooking()
    {
        $result = ['success', 'success'];

        if(!$this->rider_info = $this->getRiderInfo($this->rider_id)){
            return ['noRider', 'Rider not found'];
        }
   
        if(!$this->current_booking = $this->getNextBooking($this->rider_id)){
            return ['noBooking', 'No booking found for today'];
        }

        if(!$this->route_info = $this->getBookingRouteInfo($this->current_booking['busroute_id'])){
            return ['noRoute', 'Route not found'];
        }

        $bookingStatus = $this->isBookingStatus($this->current_booking);
        if($bookingStatus != 'booked'){
            return ['bookingStatus', "This requested booking status is: '" . $bookingStatus . "'"];
        }

        // Return the original result to allow method chaining
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
