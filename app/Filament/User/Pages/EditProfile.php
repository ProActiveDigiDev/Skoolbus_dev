<?php

namespace App\Filament\User\Pages;

use App\Models\User;
use ReflectionClass;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;


class editProfile extends Page implements HasForms
{
    use InteractsWithForms, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.user.pages.edit-profile';

    protected static ?string $navigationLabel = 'My Profile';

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public ?array $accountData = [];
    public ?array $profileData = [];

    protected function getForms(): array
    {
        return [
            'accountForm',
            'profileForm',
        ];
    }

    // public function mount(): void
    // {
    //     // abort_unless(auth()->user()->id, 403);
    //     $accountData = User::find(auth()->user()->id);
    //     // if($accountData){
    //     //     $this->form->fill($accountData->toArray(), 'accountForm');
    //     // }
        
    // }

    public function accountForm(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                ->schema([
                    Section::make('Information')
                    ->description('Basic Account Information.')
                    ->schema([
                        TextInput::make('name')
                        ->autofocus()
                        ->required()
                        ->helperText('Your Username.'),

                        TextInput::make('email')
                        ->helperText('Your email address.'),
                    ])
                    ->columns(2)
                    ->columnSpan(1),
                ])
                ->columns(1)
                ->columnSpan(1),
            ])
            ->statePath('accountData');
    }

    public function profileForm(Form $form): Form
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

                        TextInput::make('surname')
                        ->helperText('Your Surname.'),
                    ])
                    ->columns(2)
                    ->columnSpan(1),
                ])
                ->columns(1)
                ->columnSpan(1),
            ])
            ->statePath('profileData')
            ->model(UserProfile::class);
    }


    public function submit()
    {
        $state = $this->form->getState();

        $this->form->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::find(auth()->user()->id);
        $user->name = $state['name'];
        $user->email = $state['email'];
        $user->save();

        $this->redirect('/Busstop/edit-profile');

        $this->form->fill($state);        
    }

    
    public static function shouldRegisterNavigation(): bool
    {
        // return auth()->user()->hasRole(['Admin', 'Owner', 'Parent', 'Driver']);
        return true;
    }
}
