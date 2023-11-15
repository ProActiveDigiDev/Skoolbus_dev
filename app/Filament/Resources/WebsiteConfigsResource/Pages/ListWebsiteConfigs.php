<?php

namespace App\Filament\Resources\WebsiteConfigsResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\WebsiteConfigsResource;

class ListWebsiteConfigs extends ListRecords
{
    protected static string $resource = WebsiteConfigsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
