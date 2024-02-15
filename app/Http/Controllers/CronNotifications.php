<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBooking;
use Illuminate\Http\Request;

class CronNotifications extends Controller
{
    Public function cronNotificationManager($function, $arguments = null){

        //make switch for all functions
        switch($function){
            case 'tomorowBookingNotification';
                $this->tomorowBookingNotification();
                break;

            default:
                return false;
        }
        


    }

    public function tomorowBookingNotification(){
        
        $tomorrow = date("Y-m-d", strtotime("+1 day"));

        $users = User::with([
            'user_profile',
            'rider_profile.user_bookings' => function ($query) use ($tomorrow) {
                $query->where('busroute_date', $tomorrow)
                    ->where('busroute_status', 'booked')
                    ->with(['busroute.fromLocation', 'busroute.toLocation', 'busroute.timeslot']);
            },
        ])->get();

        foreach($users as $user){
            
            $parent_name = $user->user_profile->name ?? null;
            $parent_surname = $user->user_profile->surname ?? null;
            $tel = $user->user_profile->phone ?? null;
            $has_booking = [];
            $has_no_booking = [];


            
            //check if user has a profile and riders registered
            if($user->user_profile && $user->rider_profile->count() > 0){
                $message = "Good evening *" . $parent_name . " " . $parent_surname . "*.\n";

                //for each rider
                foreach($user->rider_profile as $rider){

                    //check if the rider has a booking for tomorrow
                    if($rider->user_bookings->count() > 0){
                        $message .= "*" . $rider->name . "* has the following rides scheduled for tomorrow: \n";
                        foreach($rider->user_bookings as $booking){
                            $message .= "  \xF0\x9F\x9A\x8D ```" . $booking->busroute->fromLocation->name . " to " . $booking->busroute->toLocation->name . " at " . $booking->busroute->timeslot->departure_time . "```\n";
                        }
                        $message .= "\n";

                        $has_booking[] = $rider->name;

                    }else{//if the rider has no booking for tomorrow
                        $message .= "*" . $rider->name . "* has no rides scheduled for tomorrow. \n\n";

                        $has_no_booking[] = $rider->name;
                    }

                }

                //compose ending message
                
                if(count($has_no_booking) > 0){
                    $message .= "To make a booking for " . oxfordImplode(", ", $has_no_booking) . " you can follow this link: \n";
                    $message .= "https://your.skoolbus.co.za/Busstop/booking-calendar/ \n\n";
                }
                
                if(count($has_booking) > 0 && !count($has_no_booking) > 0){
                    $message .= "Have a great evening, and we are looking forward to have " . oxfordImplode(", ", $has_booking) . " ridding with us. \n\n";
                }

                if($user && $tel){
                    sendWhatsAppNotification($tel, $message);
                }

            }

        }

    }
}
