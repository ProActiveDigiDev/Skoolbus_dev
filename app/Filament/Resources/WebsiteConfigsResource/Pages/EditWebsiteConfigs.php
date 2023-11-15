<?php

namespace App\Filament\Resources\WebsiteConfigsResource\Pages;

use App\Filament\Resources\WebsiteConfigsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteConfigs extends EditRecord
{
    protected static string $resource = WebsiteConfigsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
