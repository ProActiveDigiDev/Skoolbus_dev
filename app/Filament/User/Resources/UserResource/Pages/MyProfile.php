<?php

namespace App\Filament\User\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use App\Filament\User\Resources\UserResource;
use Filament\Forms\Concerns\InteractsWithForms;

class MyProfile extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.user.resources.user-resource.pages.my-profile';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                ->schema([
                    Section::make('Information')
                    ->description('Basic User Information.')
                    ->schema([
                        TextInput::make('name')
                        ->autofocus()
                        ->required()
                        ->helperText('Your name.'),

                        TextInput::make('email')
                        ->helperText('Your email address.'),

                        TextInput::make('password')
                        ->helperText('Your password.')
                        ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->columnSpan(1),
                ])
                ->columns(2)
                ->columnSpan(1),
            ]);
    }

    public function mount(User $user): void
    {
        abort_unless(auth()->user()->hasRole(['Admin', 'Owner', 'Parent', 'Driver']), 403);
        $this->data = $user->toArray();

        $this->form->fill($this->data);
    }
}
