<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Timeslot;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\TimeslotResource\Pages;
use App\Filament\Admin\Resources\TimeslotResource\RelationManagers;

class TimeslotResource extends Resource
{
    protected static ?string $model = Timeslot::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Rides Management';

    public static function form(Form $form): Form
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'user_admin']), 403);

        return $form
        ->schema([
            Section::make('Timeslot Info')
                ->description('Set a new timeslot starting time. This timeslot can be used with Locations to create a new route.')
                ->schema([
                    TextInput::make('name')
                    ->required()
                    ->helperText('Example: "Morning School", "Afternoon Activity" etc.')
                    ->maxLength(191),
                    
                TimePicker::make('departure_time')
                    ->datalist([
                        '05:00',
                        '05:30',
                        '06:00',
                        '06:30',
                        '07:00',
                        '07:30',
                        '08:00',
                        '08:30',
                        '09:00',
                        '09:30',
                        '10:00',
                        '10:30',
                        '11:00',
                        '11:30',
                        '12:00',
                        '12:30',
                        '13:00',
                        '13:30',
                        '14:00',
                        '14:30',
                        '15:00',
                        '15:30',
                        '16:00',
                        '16:30',
                        '17:00',
                        '17:30',
                        '18:00',
                        '18:30',
                        '19:00',
                        '19:30',
                        '20:00',
                        '20:30',
                        '21:00',
                        '21:30',
                        '22:00',
                    ])
                    ->displayFormat('H:i')
                    ->seconds(false)
                    ->required(),
    
                TextInput::make('description')
                    ->maxLength(191)
                    ->columnSpan(2),
                ])
                ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        abort_unless(auth()->user()->hasRole(['super_admin', 'user_admin']), 403);
        
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('departure_time')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Notes')
                    ->searchable(),
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
            'index' => Pages\ListTimeslots::route('/'),
            'create' => Pages\CreateTimeslot::route('/create'),
            'edit' => Pages\EditTimeslot::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'user_admin']);
    }
}
