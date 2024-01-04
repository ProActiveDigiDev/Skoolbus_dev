<?php

namespace App\Filament\User\Pages;

use App\Models\BusRoute;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class BookingCalendar extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.user.pages.booking-calendar';

    protected static ?string $navigationLabel = 'Booking Calendar';

    protected static ?string $navigationGroup = 'Booking Management';

    protected static ?string $title = 'Booking Calendar';

}
