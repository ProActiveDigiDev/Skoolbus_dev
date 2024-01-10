<?php

namespace App\Filament\User\Resources\UserBookingResource\Widgets;

use App\Models\WebsiteConfigs;
use Filament\Widgets\Widget;

class CustomerOverview extends Widget
{
    protected static string $view = 'filament.user.resources.user-booking-resource.widgets.customer-overview';

    public string $user_credits;

    public string $cost_per_credit;

    protected int | string | array $columnSpan = 2;

    public function mount()
    {
        $this->cost_per_credit = WebsiteConfigs::where('var_name', 'ride_credit_rate')->pluck('var_value')->first();
        $this->user_credits = auth()->user()->user_account->user_credits;
    }

    
    public static function canView(): bool
    {
        $panelId = filament()->getCurrentPanel()->getID();

        if($panelId === 'admin'){
            //If admin user show all riders
            return false;
        }else if($panelId === 'Busstop'){
            //If Busstop user show only riders that are assigned to the current user
            return true;
        }
    }

}
