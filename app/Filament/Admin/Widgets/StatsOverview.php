<?php

namespace App\Filament\Admin\Widgets;
 
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
 
class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '2s';


    protected function getStats(): array
    {
        return [
            Stat::make('Next Ride', $this->countdownTimer())
                ->description($this->getNextRide())
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),
            Stat::make('Parents', $this->users('parents'))
                ->description($this->users('riders') . ' Total Riders')
                ->descriptionIcon('heroicon-m-users')
                ->color($this->getColor()),
            Stat::make('Unique views', '192.1k')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }

    public function countdownTimer(){
        //Get current time
        $now = time();
        //Get the time of the next bus arrival
        $busArrival = strtotime($this->getNextRide());
        //Calculate the time difference between the two
        $difference = $busArrival - $now;
        //Format the time difference into hours, minutes and seconds
        $hours = floor($difference / 3600);
        $minutes = floor(($difference / 60) % 60);
        $seconds = $difference % 60;
        //Display the countdown
        return "$hours:$minutes:$seconds";
    }

    public function getNextRide(){
        $busArrival = "2023-11-24 09:00";
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
            return "52"; //$users;
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