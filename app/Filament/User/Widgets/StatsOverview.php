<?php

namespace App\Filament\User\Widgets;
 
use App\Models\UserAccount;
use App\Models\UserBooking;
use App\Models\CreditPurchases;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
 
class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '2s';


    protected function getStats(): array
    {
        return [
            Stat::make('credits', $this->getCredits())
                ->label('Credits available')
                ->description('Last pruchase: ' . $this->getLastCreditPurchase())
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),
            Stat::make('nextRide', $this->getNextRide())
                ->label('Next ride')
                ->description("Rides booked: " . $this->getBookingsCount())
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('primary'),
                Stat::make('riders', $this->getRiders('count'))
                ->label('Riders')
                ->description($this->getRiders('names'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }

    public function getCredits(){
        $credits = UserAccount::where('user_id', auth()->user()->id)
                    ->select('user_credits')
                    ->first();
        $credits = $credits ?? 0;
        return $credits->user_credits;   
    }

    public function getLastCreditPurchase(){
        $credits = CreditPurchases::where('user_id', auth()->user()->id)
                    ->select('updated_at')
                    ->first();
        $credits = $credits ?? 'No credits purchase yet';
        return $credits->updated_at->format('d M Y');
    }


    public function getNextRide(){
        $booking = UserBooking::where('user_id', auth()->user()->id)
                    ->whereDate('busroute_date', '>=', date('Y-m-d'))
                    ->orderBy('busroute_date', 'asc')
                    ->first();
        $booking = $booking ?? '--';
        return $booking;
    }

    public function getBookingsCount(){
        $bookings = UserBooking::where('user_id', auth()->user()->id)
                    ->count();
        $bookings = $bookings ?? 0;
        return $bookings;
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