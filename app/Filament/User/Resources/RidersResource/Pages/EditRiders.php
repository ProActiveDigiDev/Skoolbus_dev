<?php

namespace App\Filament\User\Resources\RidersResource\Pages;

use App\Filament\User\Resources\RidersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiders extends EditRecord
{
    protected static string $resource = RidersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
