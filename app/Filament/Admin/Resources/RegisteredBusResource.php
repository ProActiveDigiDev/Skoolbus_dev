<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\BusRoute;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RegisteredBus;
use App\Models\WebsiteConfigs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\RegisteredBusResource\Pages;
use App\Filament\Admin\Resources\RegisteredBusResource\RelationManagers;
use App\Filament\Admin\Resources\RegisteredBusResource\Pages\ManageRegisteredBuses;

class RegisteredBusResource extends Resource
{
    protected static ?string $model = RegisteredBus::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Rides Management';

    public static function form(Form $form): Form
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'admin_user']), 403);

        return $form
            ->schema([
                Grid::make('grid')
                ->schema([
                    Grid::make('grid')
                    ->schema([
                        FileUpload::make('bus_image')
                            ->image()
                            ->disk('busimages')
                            ->required(),
                    ])
                    ->columnSpan(1),
    
                    Grid::make('grid')
                    ->schema([
                        TextInput::make('bus_name')
                            ->required()
                            ->maxLength(191),

                        TextInput::make('bus_registration_number')
                            ->required()
                            ->maxLength(191),
                    ])
                    ->columnSpan(1),
                ])
                ->columns(2)
                ->columnSpan(2),

                Grid::make('grid')
                ->schema([
                    TextInput::make('bus_driver_name')
                        ->maxLength(191),

                    Select::make('bus_routes')
                        ->options(
                            function () {
                                return BusRoute::all()->pluck('name', 'id');
                            }
                        )
                        ->multiple()
                        ->searchable(),

                    TextInput::make('bus_capacity')
                        ->required()
                        ->default(fn () => WebsiteConfigs::where('var_name', 'ride_max_riders')->first()->var_value)
                        ->maxLength(191),

                    Select::make('bus_status')
                        ->required()
                        ->options(
                            function () {
                                return [
                                    'Active' => 'Active',
                                    'Inactive' => 'Inactive',
                                ];
                            }
                        )
                ])
                ->columns(2)
                ->columnSpan(2),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'admin_user']), 403);
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('bus_image'),
                    TextColumn::make('bus_name')
                        ->searchable(),

                    TextColumn::make('bus_registration_number')
                        ->searchable(),

                    TextColumn::make('bus_capacity'),
                    
                    IconColumn::make('bus_status')
                        ->boolean()
                        ->searchable(),
                ])                
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ManageRegisteredBuses::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin_user']);
    }
}
