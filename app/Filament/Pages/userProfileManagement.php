<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;

class userProfileManagement extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.pages.user-profile-management';

    protected static ?string $navigationLabel = 'My Profile';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

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

    public function submit()
    {
        // $this->validate();
        $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::find(auth()->user()->id);
        $user->name = $this->data['name'];
        $user->email = $this->data['email'];
        $user->password = bcrypt($this->data['password']);
        $user->save();
        $this->notify(__('Profile Updated Successfully!'), 'success');
        $this->redirect('/user-profile-management');

        $this->form->fill($this->data);        
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['Admin', 'Owner', 'Parent', 'Driver']);
    }
}
