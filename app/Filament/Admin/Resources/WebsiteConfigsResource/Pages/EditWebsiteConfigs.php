<?php

namespace App\Filament\Admin\Resources\WebsiteConfigsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Admin\Resources\WebsiteConfigsResource;

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
