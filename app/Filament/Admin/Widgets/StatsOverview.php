<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Rider;
use App\Models\UserBooking;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
 
class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '2s';


    protected function getStats(): array
    {
        return [
            Stat::make('Bookings made today', $this->bookingsMadeToday()->count())
                ->description($this->bookingsMadeSince(7)->count() . ' in the last 7 days')
                ->chart($this->bookingsMadeTodayChart(7))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Rides booked for today', $this->bookingsForToday()->count())
                ->description('Yesterday: ' . $this->bookingsForYesterday()->count() . ' | Tomorrow: ' . $this->bookingsForTomorrow()->count())
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),
            Stat::make('Total Riders', $this->ridersCount())
                ->description($this->newRidersCount() . ' new this month')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }


    public function bookingsMadeToday(){
        $bookings = UserBooking::whereDate('created_at', Carbon::today());

        return $bookings;
    }

    public function bookingsMadeSince($xDays){
        $bookings = UserBooking::whereDate('created_at', '>=', Carbon::today()->subDays($xDays));

        return $bookings;
    }

    public function bookingsMadeTodayChart($xDays){
        $data = [];
        for($i = $xDays; $i > 0; $i--){
            $date = Carbon::today()->subDays($i);
            $bookings = UserBooking::whereDate('created_at', $date)->count();
            $data[] = $bookings;
        }
        return $data;
    }

    
    public function bookingsForToday(){
        $bookings = UserBooking::whereDate('busroute_date', Carbon::today());

        return $bookings;
    }

    public function bookingsForYesterday(){
        $bookings = UserBooking::whereDate('busroute_date', Carbon::yesterday());

        return $bookings;
    }

    public function bookingsForTomorrow(){
        $bookings = UserBooking::whereDate('busroute_date', Carbon::tomorrow());

        return $bookings;
    }
    

    public function ridersCount(){
        $riders = Rider::all()->count();

        return $riders;
    }

    public function newRidersCount(){
        $riders = Rider::whereMonth('created_at', Carbon::now()->month)->count();

        return $riders;
    }

}