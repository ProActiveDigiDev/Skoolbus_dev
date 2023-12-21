<?php

namespace App\Filament\Admin\Resources\BusRouteResource\Pages;

use App\Filament\Admin\Resources\BusRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusRoute extends EditRecord
{
    protected static string $resource = BusRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
