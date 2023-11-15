<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\WebsiteConfigs;
use Illuminate\Http\UploadedFile;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
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

    protected static string $view = 'filament.pages.settings';

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
                Grid::make(2)
                ->schema([
                    Section::make('Information')
                    ->description('Basic Website Information.')
                    ->schema([
                        TextInput::make('site_name')
                        ->autofocus()
                        ->required()
                        ->helperText('The name of your website or business.'),
        
                        TextInput::make('site_tagline')
                        ->helperText('Your company Tag Line or Slogan.'),
        
                        MarkdownEditor::make('site_description')
                        ->helperText('A short description of your website.')
                        ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->columnSpan(1),
        
                    Section::make('Branding')
                    ->description('Basic Website Branding Settings.')
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
        
                        FileUpload::make('site_favicon')
                        ->image()
                        ->imageEditor()
                        ->minSize(32)
                        ->maxSize(300)
                        ->disk('branding')
                        ->visibility('public')
                        ->helperText('The Favicon of your website or business. (Not Larger than 300kb)')
                        ->columnSpan(1),

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
    
                ])
                ->columns([
                    'md' => 1,
                    'lg' => 2,
                    'xl' => 2,
                ]),
            ])
            ->statePath('data');
    }

    public function mount(WebsiteConfigs $websiteConfigs): void
    {
        abort_unless(auth()->user()->hasRole(['Admin', 'Owner']), 403);

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

        WebsiteConfigs::where('var_name', 'site_logo')
        ->update(['var_value' => 'logo.png']);

        WebsiteConfigs::where('var_name', 'site_favicon')
        ->update(['var_value' => 'favicon.png']);

        WebsiteConfigs::where('var_name', 'site_brand_color_primary')
        ->update(['var_value' => $this->data['site_brand_color_primary']]);

        WebsiteConfigs::where('var_name', 'site_brand_color_secondary')
        ->update(['var_value' => $this->data['site_brand_color_secondary']]);
        
        WebsiteConfigs::where('var_name', 'site_dark_mode')
        ->update(['var_value' => $this->data['site_dark_mode']]);

        $this->form->fill($this->data);

        //refresh the page for the changes to take effect
        return redirect()->to('/admin/settings');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['Admin', 'Owner']);
    }
}
