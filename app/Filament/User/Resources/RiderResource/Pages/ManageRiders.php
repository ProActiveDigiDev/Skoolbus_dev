<?php

namespace App\Filament\User\Resources\RiderResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\User\Resources\RiderResource;

class ManageRiders extends ManageRecords
{
    
    protected static string $resource = RiderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model): Model {
                $data['user_id'] = auth()->user()->id;
                return $model::create($data);
            })
        ];
    }
}
