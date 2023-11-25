<?php

namespace App\Filament\User\Widgets;
 
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
 
class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '2s';


    protected function getStats(): array
    {
        return [
            Stat::make('countdown', $this->countdownTimer())
                ->label('We start riding in')
                ->description($this->getNextRide())
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),
            Stat::make('riders', $this->users('riders'))
                ->label('Riders registered')
                ->description($this->users('riders') . ' Total Riders')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('bookings', '192.1k')
                ->label('Bookings made')
                ->description('Next ride: 05/02/2024 - 06:30')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
        ];
    }

    public function countdownTimer(){
        //Get current time
        $now = time();
        //Get the time of the next bus arrival
        $busArrival = strtotime($this->getNextRide());
        //Calculate the time difference between the two
        $difference = $busArrival - $now;

        //amount of days left
        $days = floor($difference / (60 * 60 * 24));

        //Display the countdown
        $days_disp = ($days > 0) ? $days . ' days ' : ' Today';
        return "$days_disp";
    }

    public function getNextRide(){
        $busArrival = "2024-02-05 06:30";
        return $busArrival;
    }

    public function users($type){
        if($type == 'total'){
            //count the total number of users from User model
            $users = \App\Models\User::count();
            return $users;
        }

        if($type == 'parents'){
            //count the total number of users from User model with role of parent_user
            // $users = \App\Models\User::where('role', 'parent_user')->count();
            return "33"; //$users;
        }

        
        if($type == 'riders'){
            //count the total number of users from User model with role of rider_user
            // $users = \App\Models\User::where('role', 'rider_user')->count();
            return "2"; //$users;
        }
    }

    public function getColor(){
        $users = $this->users('parents');
        if($users > 10){
            if($users < 20){
                return 'warning';
            }else{
                return 'success';
            }
        }else{
            return 'danger';
        }
    }
}