<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Models\User;
use App\Models\Rider;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\BusDriver;
use App\Models\UserProfile;
use App\Models\ModelHasRoles;
use App\Models\EmergencyContact;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use App\Models\EmergencyInformation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Admin\Resources\UserResource;
use App\Filament\User\Resources\RiderResource;
use Filament\Forms\Components\Checkbox;
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
    public ?array $tabNames = [];

    public ?array $riders = [];
    public ?int $selectedRiderId;

    public ?array $formNames = [
        'profileForm',
        'emergencyInformationForm',
        'emergencyContactForm',
        'riderForm',
        'driverForm',
        'userRoleForm'
    ];

    public ?array $profileData = [];
    public ?array $emergencyInformationData = [];
    public ?array $emergencyContactData = [];
    public ?array $riderData = [];
    public ?array $userRoleData = [];
    public ?array $driverData = [];


    protected function getForms(): array
    {
        $forms =  $this->getCorrectFormsForRecord();
        $this->getTabNames();
        return $forms;
    }

    public function mount(int | string $record): void
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'admin_user']), 403);

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

        //get user Role
        $userRoleData = ModelHasRoles::where('model_id', $this->record->id)->first();
        if($userRoleData){
            $this->userRoleForm->fill($userRoleData->toArray(), 'userRoleForm');
        }

        //get user driver data from bus_driver table
        $driverData = BusDriver::where('user_id', $this->record->id)->first();
        if($driverData){
            $this->driverForm->fill($driverData->toArray(), 'driverForm');
        }

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
                    ->tel()
                    ->regex('/^\+(?:\d{2}|\d{3})\d{9}$/')
                    ->helperText('Include country code. (+27) without "0"')
                    ->required(),
                    
                    TextInput::make('phone_alt')
                    ->label('Phone number (w)')
                    ->regex('/^\+(?:\d{2}|\d{3})\d{9}$/')
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
                ->hidden(fn (Get $get) => !$get('has_medical_aid'))
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
                Select::make('school')
                    ->options(
                        function(){
                            $schools = \App\Models\Location::where('destination_type', 'school')->get();
                            $schoolsArray = [];
                            foreach($schools as $school){
                                $schoolsArray[$school->id] = $school->name;
                            }
                            return $schoolsArray;
                        }
                    ),                      
            ]),
        ])
        ->statePath('riderData')
        ->model(Rider::class);
    }
    
    public function userRoleForm(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('User Role')
            ->schema([
                Grid::make()
                ->schema([
                    Select::make('role_id')
                    ->options(function(){
                        $roles = \Spatie\Permission\Models\Role::all();
                        $rolesArray = [];
                        foreach($roles as $role){
                            //skip super_admin and panel_user roles
                            if(($role->name == 'super_admin' || $role->name == 'panel_user') && !auth()->user()->hasRole(['super_admin'])){
                                continue;
                            }

                            //transform $role->name from snake_case to Title Case
                            $role->name = ucwords(str_replace('_', ' ', $role->name));
                            //add role to array
                            $rolesArray[$role->id] = $role->name;
                        }
                        return $rolesArray;
                    })
                    ->label('Role Name')
                    ->columnSpan(1),
                ])
                ->columns(2)
                ->columnSpan(2),
            ])
            ->columns(2)
            ->columnSpan(2),
        ])
        ->statePath('userRoleData')
        ->model(ModelHasRoles::class);
    }

    public function driverForm(Form $form): Form
    {
        return $form
        ->schema([
            Grid::make('basic info')
            ->schema([
                Grid::make()
                ->schema([
                    FileUpload::make('bus_driver_license')
                        ->label('Copy of Drivers License')
                        ->helperText('Upload a copy of the drivers license front and back')
                        ->disk('driverlicense')
                        ->multiple()
                        ->acceptedFileTypes(['application/pdf', 'image/png', 'image/jpg', 'image/jpeg'])
                        ->minSize(10)
                        ->maxSize(1024)
                        ->maxFiles(2)
                        ->columnSpan(1)
                        ->required(),
                ])
                ->columns(1)
                ->columnSpan(1),

                Grid::make()
                ->schema([
                    TextInput::make('bus_driver_phone')
                        ->label('Phone Number')
                        ->tel()
                        ->required()
                        ->maxLength(191)
                        ->columnSpan(2),
                    DatePicker::make('bus_driver_license_expiry')
                        ->required()
                        ->columnSpan(1),
                    Toggle::make('bus_driver_status')
                        ->label('Active')
                        ->inline(false)
                        ->columnSpan((1)),
                ])
                ->columns(2)
                ->columnSpan(1),
            ])->columns(2),
        ])
        ->statePath('driverData')
        ->model(BusDriver::class);
    }

    
    /* Form submit functions */
    public function submit()
    {
        //submit forms according to $this->formNames array
        foreach($this->formNames as $formName){
            if($formName == 'riderForm'){
                continue;
            }
            $this->{$formName.'Submit'}();
        }

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

    public function riderFormSubmit($riderId)
    {
        // Retrieve the selected rider ID and $this->riderFormData to perform necessary actions
        $riderFormState = $this->riderForm->getState();
        
        $rider = Rider::find($riderId);
        // dd($riderFormState, $rider);
        if ($rider) {
            $rider->name = $riderFormState['name'];
            $rider->surname = $riderFormState['surname'];
            $rider->id_number = $riderFormState['id_number'];
            $rider->birthday = $riderFormState['birthday'];
            $rider->phone = $riderFormState['phone'];
            $rider->school = $riderFormState['school'];
            $rider->save();
        }

        $this->riderFormFill($riderId);
    }
    
    public function userRoleFormSubmit(){

        $userRoleFormState = $this->userRoleForm->getState();

        //check if user has a role else create record
        $userRole = ModelHasRoles::where('model_id', $this->record->id);
        if(!$userRole){
            $userRole = new ModelHasRoles();
            $userRole->role_id = $userRoleFormState['role_id'];
            $userRole->model_type = 'App\Models\User';
            $userRole->model_id = $this->record->id;
            $userRole->save();
        }else{
            //update only this role_id where model_id = $this->record->id
            $userRole->update(['role_id' => $userRoleFormState['role_id']]);
        }

        $this->userRoleForm->fill($userRoleFormState);
    }

    public function driverFormSubmit()
    {
        $driverFormState = $this->driverForm->getState();
        // dd($driverFormState);

        //check if user has a driver else create record
        $driver = BusDriver::where('user_id', $this->record->id)->first();
        if(!$driver){
            $driver = new BusDriver();
            $driver->user_id = $this->record->id;
        }
        $driver->bus_driver_license = $driverFormState['bus_driver_license'];
        $driver->bus_driver_phone = $driverFormState['bus_driver_phone'];
        $driver->bus_driver_license_expiry = $driverFormState['bus_driver_license_expiry'];
        $driver->bus_driver_status = $driverFormState['bus_driver_status'] ?? 0;
        $driver->save();

        $this->driverForm->fill($driverFormState);
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

    public function getTabNames()
    {
        $forms = $this->formNames;

        foreach($forms as $form){
            //change camelCase to Capital Case
            $formName = ucfirst(preg_replace('/([a-z])([A-Z])/', '$1 $2', $form));
            //remove 'Form' from form name
            $formName = str_replace(' Form', '', $formName);
            $this->tabNames[$form] = $formName;
        }

        //insert accountInfolist at the beginning of the $this->tabNames array
        $this->tabNames = array_merge(['accountInfolist' => 'Account Info'], $this->tabNames);
        return $this->tabNames;
    }

    public function getCorrectFormsForRecord()
    {
        //get user role name
        $userRole = $this->record->roles->first()->name ?? null;

        //get all forms
        $forms = $this->formNames;

        //get correct forms according to user role
        switch ($userRole) {
            case 'driver_user':
                //remove 'emergencyInformationForm', 'emergencyContactForm', 'riderForm',
                $forms = array_diff($forms, ['emergencyInformationForm', 'emergencyContactForm', 'riderForm']);
                break;
            case 'parent_user':
                $forms = array_diff($forms, ['driverForm']);
                break;
            default:
                $forms = $forms;
        }

        $this->formNames = $forms;

        return $forms;

    }
}


