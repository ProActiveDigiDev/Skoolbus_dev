<?php

namespace App\Filament\User\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Rider;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\User\Resources\RiderResource\Pages;
use App\Filament\User\Resources\RiderResource\RelationManagers;
use App\Filament\User\Resources\RiderResource\Pages\ManageRiders;

class RiderResource extends Resource
{
    protected static ?string $model = Rider::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';

    protected static ?string $navigationLabel = 'Riders';

    protected static ?string $title = 'Riders';

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public static ?array $data = [];

    public static function getEloquentQuery(): Builder
    {
        //check if user is super_admin
        if(auth()->user()->hasRole('super_admin')){
            //get all riders
            return parent::getEloquentQuery();
        }else{
            //get only riders from the current user
            return parent::getEloquentQuery()->where('user_id', auth()->user()->id);
        }
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Grid::make()
            ->schema([
                Grid::make()
                ->schema([
                    FileUpload::make('avatar')
                        ->label('Profile Picture')
                        ->helperText('This Image will be used to identify the rider before getting on Skoolbus.')
                        ->image()
                        ->imageEditor()
                        ->avatar()
                        ->disk('useravatar')
                        ->columnSpan(1)
                        ->extraAttributes(['width' => 200, 'height' => 200, 'style' => 'margin:auto;']),
                    Grid::make()
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(191),
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label("Profile Picture")
                    ->disk('useravatar')
                    ->visibility('private')
                    ->circular()
                    ->height('70px'),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('surname')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('id_number')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('birthday')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('school')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Rider since')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRiders::route('/'),
        ];
    }
}
