<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class EditProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.user.pages.edit-profile';
}
