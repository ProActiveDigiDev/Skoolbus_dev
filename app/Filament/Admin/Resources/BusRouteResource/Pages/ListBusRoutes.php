<?php

namespace App\Filament\Admin\Resources\BusRouteResource\Pages;

use App\Filament\Admin\Resources\BusRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusRoutes extends ListRecords
{
    protected static string $resource = BusRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
