<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Location;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\LocationResource\Pages;
use App\Filament\Admin\Resources\LocationResource\RelationManagers;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Rides Management';

    public static function form(Form $form): Form
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'user_admin']), 403);

        return $form
            ->schema([
                TextInput::make('name') 
                    ->required()
                    ->maxLength(191),
                TextInput::make('description')
                    ->maxLength(191),

                Grid::make('Type')
                    ->schema([
                        Select::make('destination_type')
                            ->live()
                            ->options([
                                'home' => 'Home',
                                'school' => 'School',
                                'activity' => 'Activity',
                                'custom' => 'Custom',
                            ])
                            ->columnSpan(1),
                        TextInput::make('custom_type') //add conditional if destination_type == custom
                            ->label('Custom Destination Name')
                            ->required(fn (Get $get): bool => $get('destination_type') == 'custom')
                            ->hidden(fn (Get $get): bool => $get('destination_type') != 'custom')
                            ->maxLength(191)
                            ->columnSpan(1),
                    ])
                    ->columnSpan(1),

                    MarkdownEditor::make('address')
                    ->toolbarButtons([]),
                    
                    TextInput::make('location')
                        ->helperText('Google Maps Location')
                        ->url()
                        ->maxLength(191)
                        ->required()
                        ->columnSpan(2),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'user_admin']), 403);
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('destination_type')
                    ->searchable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'user_admin']);
    }
}
