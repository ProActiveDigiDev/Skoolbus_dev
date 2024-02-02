<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\BusDriver;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\BusDriverResource\Pages;
use App\Filament\Admin\Resources\BusDriverResource\RelationManagers;

class BusDriverResource extends Resource
{
    protected static ?string $model = BusDriver::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Rides Management';

    public static function form(Form $form): Form
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'admin_user']), 403);

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
        ]);
    }

    public static function table(Table $table): Table
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'admin_user']), 403);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bus_driver_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bus_driver_license_expiry')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('bus_driver_status')
                    ->boolean()
                    ->searchable(),
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
            'index' => Pages\ManageBusDrivers::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin_user']);
    }
}
