<?php

namespace App\Filament\Admin\Resources\BusDriverResource\Pages;

use App\Filament\Admin\Resources\BusDriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBusDrivers extends ManageRecords
{
    protected static string $resource = BusDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //,
        ];
    }
}
