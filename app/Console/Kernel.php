<?php

namespace App\Console;

use App\Http\Controllers\CronNotifications;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function(){
            $notification = new CronNotifications();
            $notification->cronNotificationManager('tomorowBookingNotification');
        })
        ->weeklyOn([0,1,2,3,4], '19:00')
        ->timezone('Africa/Johannesburg');


    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
