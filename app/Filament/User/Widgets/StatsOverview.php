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
            Stat::make('riders', $this->getRiders('count'))
                ->label('Riders registered')
                ->description($this->getRiders('names'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('bookings', '0')
                ->label('Rides booked')
                ->description('Next ride: 05/02/2024 - 06:30')
                ->descriptionIcon('heroicon-m-bell-alert')
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

    public function getRiders($type){
        if($type == 'names'){
            //Get the total number of rider from Rider model with user_id as current user
            $riders = \App\Models\Rider::where('user_id', auth()->user()->id)->get();
            $names = '';
            foreach($riders as $rider){
                $names .= $rider->name . ', ';
            }
            return $names;
        }
        
        if($type == 'count'){
            //Get the total number of rider from Rider model with user_id as current user
            $riders = \App\Models\Rider::where('user_id', auth()->user()->id)->count();
            return $riders;
        }

        return 'Error';
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