<?php

namespace App\Filament\User\Resources\RidersResource\Pages;

use App\Filament\User\Resources\RidersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiders extends ListRecords
{
    protected static string $resource = RidersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
