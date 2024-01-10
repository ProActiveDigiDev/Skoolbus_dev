<?php

namespace App\Filament\Admin\Resources\UserAccountResource\Pages;

use App\Filament\Admin\Resources\UserAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageUserAccounts extends ManageRecords
{
    protected static string $resource = UserAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
