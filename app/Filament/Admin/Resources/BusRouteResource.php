<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\BusRoute;
use App\Models\Location;
use App\Models\Timeslot;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\WebsiteConfigs;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\BusRouteResource\Pages;
use App\Filament\Admin\Resources\BusRouteResource\RelationManagers;

class BusRouteResource extends Resource
{
    protected static ?string $model = BusRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Rides Management';


    public static function form(Form $form): Form
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'admin_user']), 403);
        return $form
            ->schema([
                Section::make('Route Info')
                ->schema([
                    TextInput::make('name')
                        ->helperText('A name to identify this route by. Example: "Morning School", "Afternoon Activity" etc.')
                        ->required()
                        ->maxLength(191)
                        ->columnSpan(6),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(1)
                        ->required()
                        ->columnSpan(2),
                ])
                ->columns(8)
                ->columnSpan(2),

                Section::make('Route Locations')
                ->schema([
                    Select::make('from_location_id')
                        ->helperText('The location this route will be departing from.')
                        ->relationship('fromLocation', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),
                    Select::make('to_location_id')
                        ->helperText('The location this route will be arriving at.')
                        ->relationship('toLocation', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),
                    Select::make('timeslot_id')
                        ->helperText('The timeslot this route will be departing at.')
                        ->relationship(
                            name:'timeslot',
                            titleAttribute: 'name',
                        )
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->departure_time} ({$record->name})")
                        ->required()
                        ->columnSpan(2),
                ])
                ->columns(2)
                ->columnSpan(1),

                Section::make('Ride Details')
                ->schema([
                    TextInput::make('credits_per_ride')
                        ->helperText('The number of credits to deduct from a rider\'s account for each ride on this route.')
                        ->required()
                        ->numeric()
                        ->default(1)
                        ->columnSpan(2),
                    TextInput::make('max_riders')
                        ->helperText('The maximum number of riders allowed on this route.')
                        ->default(WebsiteConfigs::where('var_name', 'ride_max_riders')->value('var_value'))
                        ->required()
                        ->numeric()
                        ->columnSpan(2),
                    Select::make('days_active')
                        ->helperText('The days of the week this route is active.')
                        ->required()
                        ->options([
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                            'sunday' => 'Sunday',
                        ])
                        ->default(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])
                        ->multiple()
                        ->columnSpan(4),
                ])
                ->columns(4)
                ->columnSpan(1),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'admin_user']), 403);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fromLocation.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('toLocation.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('timeslot.departure_time')
                    ->description(fn (BusRoute $record): string => $record->timeslot->name)
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('credits_per_ride')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('days_active')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_riders')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
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
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),                
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
            'index' => Pages\ListBusRoutes::route('/'),
            'create' => Pages\CreateBusRoute::route('/create'),
            'edit' => Pages\EditBusRoute::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin_user']);
    }
}
