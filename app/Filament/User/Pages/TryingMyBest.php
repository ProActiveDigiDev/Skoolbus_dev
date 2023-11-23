<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class tryingMyBest extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.trying-my-best';

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public function mount(): void
    {
        $permissions = auth()->user()->getAllPermissions();
        dd($permissions);
    }

    public function boot(): void
    {
        //check if user has permission to access this page using spatie permissions package
        if (!auth()->user()->hasPermissionTo('page_TryingMyBest')) {
            abort(403);
        }

    }
}
