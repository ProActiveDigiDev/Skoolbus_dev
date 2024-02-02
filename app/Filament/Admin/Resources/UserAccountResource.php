<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserAccount;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\UserAccountResource\Pages;
use App\Filament\Admin\Resources\UserAccountResource\RelationManagers;

class UserAccountResource extends Resource
{
    protected static ?string $model = UserAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'admin_user']), 403);

        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->disabled(),
                Forms\Components\TextInput::make('user_credits')
                    ->maxLength(191)
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'admin_user']), 403);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->label('ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_credits')
                    ->label('Credits')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUserAccounts::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin_user']);
    }
}
