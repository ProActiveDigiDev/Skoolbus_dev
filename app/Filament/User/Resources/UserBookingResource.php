<?php

namespace App\Filament\User\Resources;

use Filament\Forms;
use App\Models\User;
use App\Models\Rider;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\BusRoute;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserBooking;
use App\Models\WebsiteConfigs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\User\Widgets\UserCreditsAndBookingInfo;
use App\Filament\User\Resources\UserBookingResource\Pages;
use App\Filament\User\Resources\UserBookingResource\RelationManagers;
use App\Filament\User\Resources\UserBookingResource\Widgets\CustomerOverview;

class UserBookingResource extends Resource
{
    protected static ?string $model = UserBooking::class;

    protected static ?string $navigationLabel = 'All Bookings';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Bookings';

    protected bool $isAuth = false;


    public static function getEloquentQuery(): Builder
    {
        $panelId = filament()->getCurrentPanel()->getID();

        if($panelId === 'admin'){
            //If admin user show all riders
            return parent::getEloquentQuery();
        }else if($panelId === 'Busstop'){
            //If Busstop user show only riders that are assigned to the current user
            return parent::getEloquentQuery()->where('user_id', auth()->user()->id);
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make('grid')
                ->schema([
                    Grid::make('grid')
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->options(
                                function(){
                                    return User::all()->pluck('name', 'id');
                                }
                            )
                            ->live()
                            ->searchable()
                            ->columnSpan(1),

                        Select::make('rider_id')
                            ->label('Rider')
                            ->hidden(fn(Get $get) => !$get('user_id'))  
                            ->options(
                                fn($get):array => Rider::where('user_id', $get('user_id'))->get()->pluck('name', 'id')->toArray()
                            )
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpan(2),

                    Select::make('busroute_id')
                        ->required()
                        ->relationship('busroute', 'name')
                        ->searchable()
                        ->columnSpan(1),

                    DatePicker::make('busroute_date')
                        ->required()
                        ->columnSpan(1),
                ])
                ->columnSpan(2)
                ->columns(2),
                
                Grid::make('grid')
                ->schema([
                    Select::make('busroute_status')
                        ->options(
                            function(){
                                $options = WebsiteConfigs::where('var_name', 'booking_states')->get()->pluck('var_value');
                                $optionsArr = json_decode($options[0], true);
                                return $optionsArr;
                            }
                        )
                        ->columnSpan(1),
                    
                    Grid::make('grid')
                    ->schema([                        
                        Checkbox::make('busroute_pickup')
                        ->inline(false)
                        ->columnSpan(1),
                        Checkbox::make('busroute_dropoff')
                        ->inline(false)
                        ->columnSpan(1),
                    ])
                    ->columnSpan(1)
                    ->columns(2)
                ])
                ->columns(2)
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('busroute_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rider.name')
                    ->label('Rider')
                    ->sortable(),


                /*Admin Columns*/
                Tables\Columns\SelectColumn::make('busroute.id')
                    ->label('Bus Route')
                    ->options(function(){
                        return BusRoute::all()->pluck('name', 'id');
                    })
                    ->selectablePlaceholder(false)
                    ->hidden(fn() => !auth()->user()->hasRole(['super_admin', 'admin_user']))
                    ->sortable(),
                Tables\Columns\SelectColumn::make('busroute_status')
                    ->options(
                        function(){
                            $options = WebsiteConfigs::where('var_name', 'booking_states')->get()->pluck('var_value');
                            $optionsArr = json_decode($options[0], true);
                            return $optionsArr;
                        }
                    )
                    ->selectablePlaceholder(false)
                    ->hidden(fn() => !auth()->user()->hasRole(['super_admin', 'admin_user']))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\CheckboxColumn::make('busroute_pickup')
                    ->hidden(fn() => !auth()->user()->hasRole(['super_admin', 'admin_user']))
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\CheckboxColumn::make('busroute_dropoff')
                    ->hidden(fn() => !auth()->user()->hasRole(['super_admin', 'admin_user']))
                    ->toggleable(isToggledHiddenByDefault: false),


                /*Busstop Columns*/
                Tables\Columns\TextColumn::make('busroute.name')
                    ->label('Bus Route')
                    ->hidden(fn() => auth()->user()->hasRole(['super_admin', 'admin_user']))
                    ->sortable(),
                Tables\Columns\TextColumn::make('busroute_status')
                    ->hidden(fn() => auth()->user()->hasRole(['super_admin', 'admin_user']))
                    ->badge(fn(string $state) => match($state){
                        'pending' => 'warning',
                        'booked' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('busroute_pickup')
                    ->label('Collection Status')
                    ->hidden(fn() => auth()->user()->hasRole(['super_admin', 'admin_user']))
                    ->sortable()
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('busroute_dropoff')
                    ->label('Dropoff Status')
                    ->hidden(fn() => auth()->user()->hasRole(['super_admin', 'admin_user']))
                    ->sortable()
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),


                    /*Common Columns*/
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->hidden(fn() => !auth()->user()->hasRole(['super_admin', 'admin_user']))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                
            ])
            ->filters([
                Filter::make('created_at')
                ->form([
                    Grid::make('grid')
                    ->schema([
                        Grid::make('grid')
                        ->schema([
                            DatePicker::make('From')
                            ->columnSpan(1),
                            DatePicker::make('To')
                            ->columnSpan(1),

                            Select::make('busroute_id')
                            ->label('Bus Route')
                            ->relationship('busroute', 'name')
                            ->searchable()
                            ->columnSpan(2),
                            
                            Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->columnSpan(2)
                            ->visible(fn() => auth()->user()->hasRole(['super_admin', 'admin_user'])),

                            Select::make('rider_id')
                            ->label('Rider')
                            ->relationship('rider', 'name')
                            ->searchable()
                            ->columnSpan(2),
                        ])
                        ->columns(2)
                        ->columnSpan(2),
                    ])
                ])
                ->default()
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['From'],
                            fn (Builder $query, $date): Builder => $query->whereDate('busroute_date', '>=', $date),
                        )
                        ->when(
                            $data['To'],
                            fn (Builder $query, $date): Builder => $query->whereDate('busroute_date', '<=', $date),
                        )
                        ->when(
                            $data['busroute_id'],
                            fn (Builder $query, $busroute_id): Builder => $query->where('busroute_id', $busroute_id),
                        )
                        ->when(
                            $data['user_id'],
                            fn (Builder $query, $user_id): Builder => $query->where('user_id', $user_id),
                        )
                        ->when(
                            $data['rider_id'],
                            fn (Builder $query, $rider_id): Builder => $query->where('rider_id', $rider_id),
                        );
                })
            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->filtersFormMaxHeight('300px')
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter')
                    ->slideOver(),
            )
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->hidden(fn() => !auth()->user()->hasRole(['super_admin', 'admin_user'])),
                    Tables\Actions\DeleteAction::make()->hidden(fn() => !auth()->user()->hasRole(['super_admin', 'admin_user'])),
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
            'index' => Pages\ListUserBookings::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            CustomerOverview::class,
        ];
    }


}
