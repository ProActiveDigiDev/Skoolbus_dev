<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Models\WebsiteConfigs;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WebsiteConfigsResource\Pages;
use App\Filament\Resources\WebsiteConfigsResource\RelationManagers;

class WebsiteConfigsResource extends Resource
{
    protected static ?string $model = WebsiteConfigs::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $navigationLabel = 'Website Configs (Admin)';

    protected static ?string $modelLabel = 'Website Settings';


    public static function form(Form $form): Form
    {
        abort_unless(auth()->user()->hasRole(['Admin']), 403);

        return $form
        ->schema([
            Forms\Components\TextInput::make('var_name')
            ->autofocus()
            ->required()
            ->placeholder('Enter Config Variable')
            ->label('Config Variable'),

        Forms\Components\TextInput::make('name')
            ->required()
            ->placeholder('Enter Config Title')
            ->label('Config Title'),

        Forms\Components\TextInput::make('var_value')
            ->placeholder('Enter Config Value')
            ->label('Config Value'),

        Forms\Components\TextInput::make('type')
            ->placeholder('Enter Config Type')
            ->label('Config Type'),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        abort_unless(auth()->user()->hasRole(['Admin']), 403);
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('var_name'),


                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                    
                Tables\Columns\TextColumn::make('var_value')
                    ->searchable()
                    ->sortable(),

                    
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListWebsiteConfigs::route('/'),
            'create' => Pages\CreateWebsiteConfigs::route('/create'),
            'edit' => Pages\EditWebsiteConfigs::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['Admin']);
    }
}
