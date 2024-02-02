<?php

namespace App\Livewire\Rider;

use App\Models\Rider;
use Livewire\Component;
use App\Models\BusRoute;
use App\Models\UserBooking;
use Illuminate\Http\Request;
use App\Models\WebsiteConfigs;
use Illuminate\Support\Carbon;

class RideRequest extends Component
{
    public array $website_configs = [];
    public string $rider_id = '';
    public ?Rider $rider_info = null;
    public ?UserBooking $current_booking = null;
    public ?UserBooking $next_booking = null;
    public ?BusRoute $route_info = null;
    public array $request_message = [];

    public function render(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasRole(['super_admin', 'admin_user', 'driver_user'])) {
            return redirect()->route("user-login");
        }
        

        $this->website_configs = $this->getWebsiteConfigs();

        $rider = $request->route('rider');
        $this->rider_id = encID($rider, 'decr');
        
        $this->request_message = $this->validateRequestedBooking();
        // dd($this->request_message, $this->rider_info, $this->current_booking, $this->next_booking, $this->route_info);

        return view('livewire.rider.ride-request', ['rider_id' => $this->rider_id]);
    }

    public function mount()
    {
        
    }

    public function getRiderInfo($rider_id)
    {
        $rider_info = Rider::where('id', $rider_id)->get()->first();

        return $rider_info = $rider_info ? $rider_info : null;
    }

    public function getNextBooking($rider_id)
    {
        $today = date('Y-m-d');
        $time = Carbon::now('Africa/Johannesburg');

        
        //make current time for dev purposes
        $time = Carbon::createFromFormat('H:i', '09:30', 'Africa/Johannesburg');

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

    public function acceptRequest($type)
    {
        dd($this->request_message, $type);

        $this->request_message = ['success', 'DONE'];
        $bookingStatus = $this->updateBookingStatus();

        if($bookingStatus == 'success'){
            $this->addRiderToCurrentRoute();        
            $this->sendNotification();
        }

        return $bookingStatus;

    }

    public function rejectRequest()
    {
        //change booking status to cancelled

    }

    public function updateBookingStatus()
    {
        //change booking status to intransit
        $this->current_booking['busroute_status'] = 'intransit';

        //change busroute_pickup to true
        $this->current_booking['busroute_pickup'] = true;

        //save the changes
        $this->current_booking->save();

        return 'success';
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
}
