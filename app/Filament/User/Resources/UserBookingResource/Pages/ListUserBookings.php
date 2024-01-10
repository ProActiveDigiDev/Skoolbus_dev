<?php

namespace App\Filament\User\Resources\UserBookingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\User\Resources\UserBookingResource;
use App\Filament\User\Resources\UserBookingResource\Widgets\CustomerOverview;

class ListUserBookings extends ListRecords
{
    protected static string $resource = UserBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
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
