<?php

namespace App\Filament\User\Resources\UserResource\Pages;

use Pages\MyProfile;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\User\Resources\UserResource;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            // ...
            'sort' => MyProfile::route('/sort'),
        ];
    }
}
