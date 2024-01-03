<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Models\Rider;
use Filament\Forms\Form;
use App\Models\UserProfile;
use App\Models\EmergencyContact;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use App\Models\EmergencyInformation;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Admin\Resources\UserResource;
use App\Filament\User\Resources\RiderResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Infolists\Components\Section as infolistSection;


class ManageUserProfile extends Page implements HasForms
{
    use InteractsWithForms, InteractsWithRecord;
    
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.admin.resources.user-resource.pages.manage-user-profile';

    protected int $recordUser;

    public ?string $activeTab = 'tab_1'; // Put your default tab here

    public ?array $riders = [];
    public ?int $selectedRiderId;

    public ?array $profileData = [];
    public ?array $emergencyInformationData = [];
    public ?array $emergencyContactData = [];
    public ?array $riderData = [];

    protected function getForms(): array
    {
        return [
            'profileForm',
            'emergencyInformationForm',
            'emergencyContactForm',
            'riderForm'
        ];
    }

    public function mount(int | string $record): void
    {
        abort_unless(auth()->user()->id, 403);

        $this->record = $this->resolveRecord($record);
 
        static::authorizeResourceAccess();

        //get user profile data from user_profile table
        $profileData = UserProfile::where('user_id', $this->record->id)->first();
        if($profileData){
            $this->profileForm->fill($profileData->toArray(), 'profileForm');
        }

        //get user emergency information data from emergency_information table
        $emergencyInformationData = EmergencyInformation::where('user_id', $this->record->id)->first();
        if($emergencyInformationData){
            $this->emergencyInformationForm->fill($emergencyInformationData->toArray(), 'emergencyInformationForm');
        }

        //get user emergency contact data from emergency_contact table
        $emergencyContactData = EmergencyContact::where('user_id', $this->record->id)->first();
        if($emergencyContactData){
            $this->emergencyContactForm->fill($emergencyContactData->toArray(), 'emergencyContactForm');
        }
        
        //get all riders profiles associated with the user
        $this->riders = $this->riderData();

    }


    /* Form schemas */
    public function accountInfolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->record($this->record)
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
                    ->tel(),
            
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
                Toggle::make('has_medical_aid'),

                Grid::make()
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

    public static function riderForm(Form $form): Form
    {
        return $form
        ->schema([
            Grid::make()
            ->schema([
                Grid::make()
                ->schema([
                    Placeholder::make('avatar')
                        ->label('Profile Picture')
                        ->content(new HtmlString('<h4>The Profile Picture can only be changed <a style="text-decoration:underline;" href="/admin/riders">Here</a>.</h4>'))
                        ->columnSpan(1),
                    Grid::make()
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(191)
                            ->required(),
                        TextInput::make('surname')
                            ->maxLength(191)
                    ])->columnSpan(1)->columns(1),
                ])
                ->columns(2),
                TextInput::make('id_number')
                    ->label('ID Number')
                    ->helperText("If applicable")
                    ->maxLength(191),
                DatePicker::make('birthday')
                    ->format('d/M/Y')
                    ->displayFormat('d/M/Y')
                    ->native(false),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(191),
                TextInput::make('school')
                    ->maxLength(191),                        
            ]),
        ])
        ->statePath('riderData')
        ->model(Rider::class);
    }

    
    /* Form submit functions */
    public function submit()
    {
        $this->profileFormSubmit();
        $this->emergencyInformationFormSubmit();
        $this->emergencyContactFormSubmit(); 
    }

    public function profileFormSubmit()
    {
        $profileFormState = $this->profileForm->getState();

        //check if user has a profile else create record
        $userProfile = UserProfile::where('user_id', $this->record->id)->first();
        if(!$userProfile){
            $userProfile = new UserProfile();
            $userProfile->user_id = $this->record->id;
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

        $this->profileForm->fill($profileFormState);        
    }

    public function emergencyInformationFormSubmit(){
        $emergencyInformationFormState = $this->emergencyInformationForm->getState();

        //check if user has a emergency information else create record
        $emergencyInformation = EmergencyInformation::where('user_id', $this->record->id)->first();
        if(!$emergencyInformation){
            $emergencyInformation = new EmergencyInformation();
            $emergencyInformation->user_id = $this->record->id;
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
        $emergencyContact = EmergencyContact::where('user_id', $this->record->id)->first();
        if(!$emergencyContact){
            $emergencyContact = new EmergencyContact();
            $emergencyContact->user_id = $this->record->id;
        }
        $emergencyContact->ec_name = $emergencyContactFormState['ec_name'];
        $emergencyContact->ec_surname = $emergencyContactFormState['ec_surname'];
        $emergencyContact->ec_id_number = $emergencyContactFormState['ec_id_number'];
        $emergencyContact->ec_contact_number = $emergencyContactFormState['ec_contact_number'];
        $emergencyContact->ec_relationship = $emergencyContactFormState['ec_relationship'];
        $emergencyContact->save();

        $this->emergencyContactForm->fill($emergencyContactFormState);
    }

    public function riderFormSubmit()
    {
        // Retrieve the selected rider ID and $this->riderFormData to perform necessary actions
        $riderId = $this->selectedRiderId;
        $riderFormState = $this->riderForm->getState();

        $rider = Rider::find($riderId);
        if ($rider) {
            $rider->avatar = $riderFormState['avatar']; 
            $rider->name = $riderFormState['name'];
            $rider->surname = $riderFormState['surname'];
            $rider->id_number = $riderFormState['id_number'];
            $rider->birthday = $riderFormState['birthday'];
            $rider->phone = $riderFormState['phone'];
            $rider->school = $riderFormState['school'];
            $rider->save();
        }

        $this->riderForm->fill($riderFormState);
    }

    /* Additional functions */

    public function riderData()
    {
        //get all riders profiles associated with the user
        $ridersCollection = Rider::where('user_id', $this->record->id)->get();

        //get all riders ids and names associated with the user
        $riders = [];
        foreach ($ridersCollection as $rider) {
            $riderObj = (object) [
                'id' => $rider->id,
                'name' => $rider->name,
            ];
            $riders[] = $riderObj;
        }

        return $riders;
    }

    public function riderFormFill($riderId)
    {        
        // Store the selected rider ID
        $this->selectedRiderId = $riderId;

        //get rider data from rider table
        $riderData = Rider::find($riderId);
        if($riderData){
            $this->riderForm->fill($riderData->toArray(), 'riderForm');
            
        }
    }

    public function removeCurrentImage()
    {  
        $imgUrl = UserProfile::where('user_id', $this->record->id)->first()->avatar ?? null;
        if($imgUrl){
            Storage::disk('useravatar')->delete($imgUrl);
            return true;

        }else{
            return false;
        }
    }
}


