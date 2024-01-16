<?php

namespace App\Filament\Admin\Resources\RegisteredBusResource\Pages;

use App\Filament\Admin\Resources\RegisteredBusResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRegisteredBuses extends ManageRecords
{
    protected static string $resource = RegisteredBusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
