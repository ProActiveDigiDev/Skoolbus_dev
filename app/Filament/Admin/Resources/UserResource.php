<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Faker\Provider\ar_EG\Text;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';


    public static function getEloquentQuery(): Builder
    {
        //show only users that has not role super_admin unless the current user is super_admin
        if(auth()->user()->hasRole('super_admin')){
            return parent::getEloquentQuery();
        }else{
            return parent::getEloquentQuery()->whereDoesntHave('roles', function($query){
                $query->where('name', 'super_admin');
            });
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Select::make('roles')
                    ->relationship('roles', 'name', 
                    fn (Builder $query) => $query->whereNot('name', 'super_admin')->whereNot('name', 'panel_user'))
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->since()
                    ->searchable(),
                TextColumn::make('rider_profile_count')
                ->label('Riders')
                ->counts('rider_profile'),
                IconColumn::make('roles')
                    ->label('Roles')
                    ->icon(function ($state){
                        $role = $state[0]['name'];
                        switch ($role) {
                            case 'super_admin':
                                return 'heroicon-s-shield-exclamation';
                                break;
                            case 'admin_user':
                                return 'heroicon-o-shield-check';
                                break;
                            case 'driver_user':
                                return 'heroicon-o-truck';
                                break;
                            case 'parent_user':
                                return 'heroicon-o-user';
                                break;
                            case 'panel_user':
                                return 'heroicon-s-exclamation-circle';
                                break;
                            default:
                                return 'heroicon-s-exclamation-circle';
                                break;
                        }
                    })
                    ->color(function($state){
                        $role = $state[0]['name'];
                        switch ($role) {
                            case 'super_admin':
                                return 'danger';
                                break;
                            case 'admin_user':
                                return 'warning';
                                break;
                            case 'driver_user':
                                return 'info';
                                break;
                            case 'parent_user':
                                return 'success';
                                break;
                            case 'panel_user':
                                return 'danger';
                                break;
                            default:
                                return 'danger';
                                break;
                        }
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('roles')
                ->form([
                    Select::make('roles')
                        ->label('Filter by Role')
                        ->relationship('roles', 'name')
                        ->options( function(){
                           return \Spatie\Permission\Models\Role::whereNot('name', 'super_admin')->whereNot('name', 'panel_user')
                           ->get()
                           ->pluck('name', 'id');

                        })
                        ->multiple()
                        ->columnSpan(2),
                ])
                ->default()
                ->query(function (Builder $query, array $data): Builder {
                    //make a string from the array of roles
                    $role = $data['roles'];

                    return $query
                        ->when(
                            $role,
                            // fn (Builder $query, array $role) => $query->whereHas('roles', fn ($query) => $query->where('id', $role))
                            fn (Builder $query, array $role) => $query->whereHas('roles', fn ($query) => $query->whereIn('id', $role))
                        );
                })
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(
                //redirect to manage user profile page for this record
                fn(Model $record): string => route('filament.admin.resources.users.manage_user_profile', $record)
            );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'manage_user_profile' => Pages\ManageUserProfile::route('/{record}/manage_user_profile'),
        ];
    }
}