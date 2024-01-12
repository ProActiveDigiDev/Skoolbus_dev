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
        //check if user is admin
        $panelId = filament()->getCurrentPanel()->getID();

        if($panelId === 'admin'){
            return [
                Actions\CreateAction::make(),
            ];
        }else if($panelId === 'Busstop'){
            return [];
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
