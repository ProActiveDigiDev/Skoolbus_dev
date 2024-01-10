<?php

namespace App\Filament\User\Resources\UserBookingResource\Pages;

use App\Filament\User\Resources\UserBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserBooking extends EditRecord
{
    protected static string $resource = UserBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
