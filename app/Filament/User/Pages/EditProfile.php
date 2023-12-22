<?php

namespace App\Filament\User\Pages;

use App\Models\User;
use ReflectionClass;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\UserProfile;
use App\Models\EmergencyContact;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Grid;
use App\Models\EmergencyInformation;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Concerns\InteractsWithForms;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Infolists\Components\Grid as infolistGrid;
use Filament\Infolists\Components\Section as infolistSection;


class editProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.user.pages.edit-profile';

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $title = 'My Profile';

    public ?string $activeTab = 'tab_1'; // Put your default tab here

    public ?array $profileData = [];
    public ?array $emergencyInformationData = [];
    public ?array $emergencyContactData = [];

    protected function getForms(): array
    {
        return [
            'profileForm',
            'emergencyInformationForm',
            'emergencyContactForm',
        ];
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->id, 403);

        //get user profile data from user_profile table
        $profileData = UserProfile::where('user_id', auth()->user()->id)->first();
        if($profileData){
            $this->profileForm->fill($profileData->toArray(), 'profileForm');
        }

        //get user emergency information data from emergency_information table
        $emergencyInformationData = EmergencyInformation::where('user_id', auth()->user()->id)->first();
        if($emergencyInformationData){
            $this->emergencyInformationForm->fill($emergencyInformationData->toArray(), 'emergencyInformationForm');
        }

        //get user emergency contact data from emergency_contact table
        $emergencyContactData = EmergencyContact::where('user_id', auth()->user()->id)->first();
        if($emergencyContactData){
            $this->emergencyContactForm->fill($emergencyContactData->toArray(), 'emergencyContactForm');
        }
        
    }


    /* Form schemas */
    public function accountInfolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->record(User::find(auth()->user()->id))
        ->schema([
            infolistSection::make('')
            ->schema([
                TextEntry::make('name')
                ->label('Username')
                ->columnSpan(1),

                TextEntry::make('email')
                ->columnSpan(1),
            ])
            ->columns(2)
        ]);
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
                    TextInput::make('phone')
                    ->label('Phone number')
                    ->tel()
                    ->required(),
            
                    TextInput::make('phone_alt')
                    ->label('Phone number (w)')
                    ->tel(),

                    TextInput::make('email')
                    ->label('Email address (w)')
                    ->email()
                    ->maxLength(255)
                    ->columnSpan(2),
            
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
    
    public function emergencyInformationForm(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Medical Aid Information')
            ->schema([
                Toggle::make('has_medical_aid')
                ->live(),

                Grid::make()
                ->hidden(fn (array $state) => ! $state['has_medical_aid'])
                ->schema([
                    TextInput::make('medical_aid_name')
                    ->label('Medical Aid Name')
                    ->columnSpan(1),

                    TextInput::make('medical_aid_plan')
                    ->label('Medical Aid Plan')
                    ->columnSpan(1),

                    TextInput::make('medical_aid_main_member_name')
                    ->label('Main Member Name')
                    ->columnSpan(1),

                    TextInput::make('medical_aid_main_member_number')
                    ->label('Main Member Number')
                    ->columnSpan(1),

                    TextInput::make('medical_aid_dependants')
                    ->label('Dependants')
                    ->columnSpan(1),

                    TextInput::make('medical_aid_dependants')
                    ->label('Dependants')
                    ->columnSpan(1),
                ])
                ->columns(2)
                ->columnSpan(2),
            ])
            ->columns(2)
            ->columnSpan(2),
        ])
        ->statePath('emergencyInformationData')
        ->model(EmergencyInformation::class);
    }

    public function emergencyContactForm(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Emergency Contact')
            ->schema([
                Grid::make()
                ->schema([
                    TextInput::make('ec_name')
                    ->label('Name')
                    ->columnSpan(1),

                    TextInput::make('ec_surname')
                    ->label('Surname')
                    ->columnSpan(1),

                    TextInput::make('ec_id_number')
                    ->label('ID number')
                    ->columnSpan(1),

                    TextInput::make('ec_contact_number')
                    ->label('Phone number')
                    ->tel()
                    ->columnSpan(1),

                    TextInput::make('ec_relationship')
                    ->label('Relationship')
                    ->columnSpan(2),
                ])
                ->columns(2)
                ->columnSpan(2),
            ])
            ->columns(2)
            ->columnSpan(2),
        ])
        ->statePath('emergencyContactData')
        ->model(EmergencyContact::class);
    }

    
    /* Form submit functions */
    public function submit()
    {
        $this->profileFormSubmit();
        $this->emergencyInformationFormSubmit();
        $this->emergencyContactFormSubmit();

        $this->redirect('/Busstop/edit-profile');  
    }

    public function profileFormSubmit()
    {
        $profileFormState = $this->profileForm->getState();

        //check if user has a profile else create record
        $userProfile = UserProfile::where('user_id', auth()->user()->id)->first();
        if(!$userProfile){
            $userProfile = new UserProfile();
            $userProfile->user_id = auth()->user()->id;
        }
        $userProfile->name = $profileFormState['name'];
        $userProfile->surname = $profileFormState['surname'];
        $userProfile->id_number = $profileFormState['id_number'];
        $userProfile->email = $profileFormState['email'];
        $userProfile->phone = $profileFormState['phone'];
        $userProfile->phone_alt = $profileFormState['phone_alt'];
        $userProfile->address = $profileFormState['address'];
        $userProfile->avatar = $profileFormState['avatar'];
        $this->removeCurrentImage();
        $userProfile->save();

        //TODO: check if user has a rider profile else create record
        //TODO: fix bugs on above todo

        $this->profileForm->fill($profileFormState);        
    }

    public function emergencyInformationFormSubmit(){
        $emergencyInformationFormState = $this->emergencyInformationForm->getState();

        //check if user has a emergency information else create record
        $emergencyInformation = EmergencyInformation::where('user_id', auth()->user()->id)->first();
        if(!$emergencyInformation){
            $emergencyInformation = new EmergencyInformation();
            $emergencyInformation->user_id = auth()->user()->id;
        }
        $emergencyInformation->has_medical_aid = $emergencyInformationFormState['has_medical_aid'];
        $emergencyInformation->medical_aid_name = $emergencyInformationFormState['medical_aid_name'] ?? null;
        $emergencyInformation->medical_aid_plan = $emergencyInformationFormState['medical_aid_plan'] ?? null;
        $emergencyInformation->medical_aid_main_member_name = $emergencyInformationFormState['medical_aid_main_member_name'] ?? null;
        $emergencyInformation->medical_aid_main_member_number = $emergencyInformationFormState['medical_aid_main_member_number'] ?? null;
        $emergencyInformation->medical_aid_dependants = $emergencyInformationFormState['medical_aid_dependants'] ?? null;
        $emergencyInformation->save();

        $this->emergencyInformationForm->fill($emergencyInformationFormState);
    }

    public function emergencyContactFormSubmit(){
        $emergencyContactFormState = $this->emergencyContactForm->getState();

        //check if user has a emergency contact else create record
        $emergencyContact = EmergencyContact::where('user_id', auth()->user()->id)->first();
        if(!$emergencyContact){
            $emergencyContact = new EmergencyContact();
            $emergencyContact->user_id = auth()->user()->id;
        }
        $emergencyContact->ec_name = $emergencyContactFormState['ec_name'];
        $emergencyContact->ec_surname = $emergencyContactFormState['ec_surname'];
        $emergencyContact->ec_id_number = $emergencyContactFormState['ec_id_number'];
        $emergencyContact->ec_contact_number = $emergencyContactFormState['ec_contact_number'];
        $emergencyContact->ec_relationship = $emergencyContactFormState['ec_relationship'];
        $emergencyContact->save();

        $this->emergencyContactForm->fill($emergencyContactFormState);
    }

    /* Additional functions */
    public function removeCurrentImage()
    {  
        $imgUrl = UserProfile::where('user_id', auth()->user()->id)->first()->avatar ?? null;
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


