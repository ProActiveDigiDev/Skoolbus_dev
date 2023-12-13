<?php

namespace App\Filament\Admin\Resources\TimeslotResource\Pages;

use App\Filament\Admin\Resources\TimeslotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimeslots extends ListRecords
{
    protected static string $resource = TimeslotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
