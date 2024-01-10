<?php

namespace App\Filament\User\Pages;

use App\Models\BusRoute;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\User\Resources\UserBookingResource\Widgets\CustomerOverview;

class BookingCalendar extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.user.pages.booking-calendar';

    protected static ?string $navigationLabel = 'Booking Calendar';

    protected static ?string $navigationGroup = 'Booking Management';

    protected static ?string $title = 'Booking Calendar';

    public function mount(): void
    {
        abort_unless(function(): bool
        {
            $panelId = filament()->getCurrentPanel()->getID();
    
            if($panelId === 'admin' && auth()->user()->id){
                return false;
            }else if($panelId === 'Busstop' && auth()->user()->id){
                return true;
            }
        }, 403);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $panelId = filament()->getCurrentPanel()->getID();

        if($panelId === 'admin'){
            return false;
        }else if($panelId === 'Busstop'){
            return true;
        }
    }

    public static function getWidgets(): array
    {
        return [
            CustomerOverview::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerOverview::class,
        ];
    }
    
}
