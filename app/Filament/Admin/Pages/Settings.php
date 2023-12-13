<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\WebsiteConfigs;
use Illuminate\Http\UploadedFile;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;



class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    // public UploadedFile $image;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.settings';

    protected static ?string $navigationLabel = 'Website Configs';

    protected static ?string $modelLabel = 'Website Configurations';

    public ?array $data = [];
    
    protected $rules = [
        'site_name' => 'required'
    ];
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Information')
                            ->schema([
                                Section::make('Website Info')
                                ->description('Basic Website Information.')
                                ->schema([
                                    TextInput::make('site_name')
                                    ->autofocus()
                                    ->required()
                                    ->helperText('The name of your website or business.'),
                    
                                    TextInput::make('site_tagline')
                                    ->helperText('Your company Tag Line or Slogan.'),
                    
                                    MarkdownEditor::make('site_description')
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bulletList',
                                        'heading',
                                        'bold',
                                        'italic',
                                        'strike',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'undo',
                                    ])
                                    ->helperText('A short description of your website.')
                                    ->columnSpan(2),
                                ])
                                ->columns(2)
                                ->columnSpan(1),

                                Section::make('Contact Information')
                                ->description('Your business contact info.')
                                ->schema([
                                    TextInput::make('site_contact_nr')
                                    ->label('Contact Number')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->required(),
                                    
                                    TextInput::make('site_contact_nr1')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->label('Contact Number (alt)'),
                                    
                                    TextInput::make('site_contact_email1')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->label('Contact Email')
                                    ->required(),
                                    
                                    TextInput::make('site_contact_email2')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->label('Contact Email (alt)')
                                    ->required(),
                                ])
                                ->columns(2)
                                ->columnSpan(1),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Branding')
                            ->schema([
                                Section::make('Logos')
                                ->description('All your logos.')
                                ->schema([
                                    FileUpload::make('site_logo')
                                    ->image()
                                    ->imageEditor()
                                    ->minSize(32)
                                    ->maxSize(1024)
                                    ->disk('branding')
                                    ->visibility('public')
                                    ->helperText('The Logo of your website or business. (Not Larger than 1MB)')
                                    ->columnSpan(1),

                                    FileUpload::make('site_dark_logo')
                                    ->image()
                                    ->imageEditor()
                                    ->minSize(32)
                                    ->maxSize(1024)
                                    ->disk('branding')
                                    ->visibility('public')
                                    ->helperText('The Logo to be used on dark backgrounds. (Not Larger than 1MB)')
                                    ->columnSpan(1),
                    
                                    FileUpload::make('site_favicon')
                                    ->image()
                                    ->imageEditor()
                                    ->minSize(32)
                                    ->maxSize(300)
                                    ->avatar()
                                    ->disk('branding')
                                    ->visibility('public')
                                    ->helperText('The Favicon of your website or business. (Not Larger than 300kb)')
                                    ->columnSpan(2),
                                ])
                                ->columns(2)
                                ->columnSpan(1),

                                Section::make('Colours')
                                ->description('Your brand colours Colours.')
                                ->schema([
                                    ColorPicker::make('site_brand_color_primary')
                                    ->helperText('The Primary Color of your website or business.')
                                    ->dehydrated(false)
                                    ->columnSpan(1),
                    
                                    ColorPicker::make('site_brand_color_secondary')
                                    ->helperText('The Secondary Color of your website or business. (HEX Color Code)')
                                    ->columnSpan(1),
                                ])
                                ->columns(2)
                                ->columnSpan(1),
                            ])->columns(2),

                        Tabs\Tab::make('General Settings')
                            ->schema([
                                Section::make('General Settings')
                                ->description('General Website Settings.')
                                ->schema([
                                    Toggle::make('site_dark_mode')
                                    ->onIcon('heroicon-m-check')
                                    ->offIcon('heroicon-m-x-mark')
                                    ->helperText('Allow users to select Dark Mode.'),
                                ])
                                ->columns(2)
                                ->columnSpan(1),
                            ]),

                        Tabs\Tab::make('Social Media')
                            ->schema([
                                Section::make('Social Media')
                                ->description('Social Media Links. <br> Insert the full URL to your Social Media Pages.')
                                ->schema([
                                    TextInput::make('site_social_links_facebook')
                                    ->label('Facebook')
                                    ->url(),

                                    TextInput::make('site_social_links_instagram')
                                    ->label('Instagram')
                                    ->url(),

                                    TextInput::make('site_social_links_linkedin')
                                    ->label('LinkedIn')
                                    ->url(),

                                    TextInput::make('site_social_links_youtube')
                                    ->label('YouTube')
                                    ->url(),

                                    TextInput::make('site_social_links_tiktok')
                                    ->label('TikTok')
                                    ->url(),
                                ])
                                ->columns(2)
                                ->columnSpan(1),

                            ]),

                        Tabs\Tab::make('Rides Settings')
                        ->schema([
                            Section::make('Rides Settings')
                            ->description('Configurations for all Ride related settings.')
                            ->schema([
                                TextInput::make('ride_credit_rate')
                                ->numeric()
                                ->prefix('R')
                                ->helperText('What is the rate for a single Ride Credit? (in ZAR)'),
                            ])
                            ->columns(4)
                            ->columnSpan(1),
                        ]),

                    ])->contained(false)
            ])->statePath('data');
    }

    public function mount(WebsiteConfigs $websiteConfigs): void
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'user_admin']), 403);

        foreach ($websiteConfigs::all() as $config) {
            $this->data[$config->var_name] = $config->var_value;
        }

        $this->form->fill($this->data);
    }

    public function submit()
    {
        foreach ($this->data['site_logo'] as $logo) {
            if($logo instanceof TemporaryUploadedFile){
                $fileContent = file_get_contents($logo->getRealPath());
                Storage::disk('branding')->put('logo.png', $fileContent);
            }
        }
        foreach ($this->data['site_dark_logo'] as $logo) {
            if($logo instanceof TemporaryUploadedFile){
                $fileContent = file_get_contents($logo->getRealPath());
                Storage::disk('branding')->put('logo_dark.png', $fileContent);
            }
        }
        foreach ($this->data['site_favicon'] as $favicon) {
            if($favicon instanceof TemporaryUploadedFile){
                $fileContent = file_get_contents($favicon->getRealPath());
                Storage::disk('branding')->put('favicon.png', $fileContent);
            }
        }

        
        
        WebsiteConfigs::where('var_name', 'site_name')
        ->update(['var_value' => $this->data['site_name']]);

        WebsiteConfigs::where('var_name', 'site_tagline')
        ->update(['var_value' => $this->data['site_tagline']]);

        WebsiteConfigs::where('var_name', 'site_description')
        ->update(['var_value' => $this->data['site_description']]);

        WebsiteConfigs::where('var_name', 'site_contact_nr')
        ->update(['var_value' => $this->data['site_contact_nr']]);

        WebsiteConfigs::where('var_name', 'site_contact_nr1')
        ->update(['var_value' => $this->data['site_contact_nr1']]);

        WebsiteConfigs::where('var_name', 'site_contact_email1')
        ->update(['var_value' => $this->data['site_contact_email1']]);

        WebsiteConfigs::where('var_name', 'site_contact_email2')
        ->update(['var_value' => $this->data['site_contact_email2']]);

        WebsiteConfigs::where('var_name', 'site_logo')
        ->update(['var_value' => 'logo.png']);

        WebsiteConfigs::where('var_name', 'site_dark_logo')
        ->update(['var_value' => 'logo_dark.png']);

        WebsiteConfigs::where('var_name', 'site_favicon')
        ->update(['var_value' => 'favicon.png']);

        WebsiteConfigs::where('var_name', 'site_brand_color_primary')
        ->update(['var_value' => $this->data['site_brand_color_primary']]);

        WebsiteConfigs::where('var_name', 'site_brand_color_secondary')
        ->update(['var_value' => $this->data['site_brand_color_secondary']]);
        
        WebsiteConfigs::where('var_name', 'site_dark_mode')
        ->update(['var_value' => $this->data['site_dark_mode']]);
        
        WebsiteConfigs::where('var_name', 'site_social_links_facebook')
        ->update(['var_value' => $this->data['site_social_links_facebook']]);

        WebsiteConfigs::where('var_name', 'site_social_links_instagram')
        ->update(['var_value' => $this->data['site_social_links_instagram']]);

        WebsiteConfigs::where('var_name', 'site_social_links_linkedin')
        ->update(['var_value' => $this->data['site_social_links_linkedin']]);

        WebsiteConfigs::where('var_name', 'site_social_links_youtube')
        ->update(['var_value' => $this->data['site_social_links_youtube']]);

        WebsiteConfigs::where('var_name', 'site_social_links_tiktok')
        ->update(['var_value' => $this->data['site_social_links_tiktok']]);
        
        WebsiteConfigs::where('var_name', 'ride_credit_rate')
        ->update(['var_value' => $this->data['ride_credit_rate']]);

        $this->form->fill($this->data);

        //refresh the page for the changes to take effect
        return redirect()->to('/admin/settings');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'user_admin']);
    }
}
