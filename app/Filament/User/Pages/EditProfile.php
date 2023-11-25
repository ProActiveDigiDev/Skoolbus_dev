<?php

namespace App\Filament\User\Pages;

use App\Models\User;
use ReflectionClass;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\UserProfile;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
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

    public function mount(): void
    {
        // abort_unless(auth()->user()->id, 403);
        $accountData = User::find(auth()->user()->id);
        if($accountData){
            $this->accountForm->fill($accountData->toArray(), 'accountForm');
        }
        //get user profile data from user_profile table
        $profileData = UserProfile::where('user_id', auth()->user()->id)->first();
        if($profileData){
            $this->profileForm->fill($profileData->toArray(), 'profileForm');
        }
        
    }

    public function accountForm(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                ->schema([
                    Section::make('User Account')
                    ->description('Basic Account Information.')
                    ->schema([
                        TextInput::make('name')
                        ->autofocus()
                        ->required()
                        ->label('Username.')
                        ->maxLength(255),

                        TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required()
                        ->maxLength(255),
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
            Section::make('Profile')
            ->schema([
                Grid::make()
                ->schema([
                    FileUpload::make('avatar')
                    ->label('Profile Picture')
                    ->image()
                    ->imageEditor()
                    ->avatar()
                    ->disk('useravatar')
                    ->columnStart(2),
                ])
                ->columns(3)
                ->columnSpan(1),

                Grid::make()
                ->schema([
                    TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255),

                    TextInput::make('surname')
                    ->maxLength(255),

                    TextInput::make('id_number')
                    ->label('ID number')
                    ->maxLength(255),

                ])
                ->columns(1)
                ->columnSpan(1),

                Grid::make()
                ->schema([
                    TextInput::make('email')
                    ->label('Email address (2)')
                    ->email()
                    ->maxLength(255)
                    ->columnSpan(2),

                    TextInput::make('phone')
                    ->label('Phone number')
                    ->tel()
                    ->required(),
            
                    TextInput::make('phone_alt')
                    ->label('Phone number (2)')
                    ->tel(),
            
                    Textarea::make('address')
                    ->label('Home address')
                    ->maxLength(255)
                    ->columnSpan(2), 
                ])
                ->columns(2)
                ->columnSpan(2),
            ])
            ->columns(2)
        ])
        ->statePath('profileData')
        ->model(UserProfile::class);
    }


    public function submit()
    {
        $this->accountFormSubmit();
        $this->profileFormSubmit();

        $this->redirect('/Busstop/edit-profile');  
    }

    public function accountFormSubmit()
    {
        $accountFormState = $this->accountForm->getState();

        $user = User::find(auth()->user()->id);
        $user->name = $accountFormState['name'];
        $user->email = $accountFormState['email'];
        $user->save();

        $this->accountForm->fill($accountFormState);        
    }

    public function profileFormSubmit()
    {
        $profileFormState = $this->profileForm->getState();

        $user = UserProfile::where('user_id', auth()->user()->id)->first();
        $user->name = $profileFormState['name'];
        $user->surname = $profileFormState['surname'];
        $user->id_number = $profileFormState['id_number'];
        $user->email = $profileFormState['email'];
        $user->phone = $profileFormState['phone'];
        $user->phone_alt = $profileFormState['phone_alt'];
        $user->address = $profileFormState['address'];
        $user->avatar = $profileFormState['avatar'];
        $this->removeCurrentImage();
        $user->save();

        $this->profileForm->fill($profileFormState);        
    }

    public function removeCurrentImage()
    {  
        $imgUrl = UserProfile::where('user_id', auth()->user()->id)->first()->avatar;
        if($imgUrl){
            Storage::disk('useravatar')->delete($imgUrl);
            return true;

        }else{
            return false;
        }
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        // return auth()->user()->hasRole(['Admin', 'Owner', 'Parent', 'Driver']);
        return true;
    }
}




