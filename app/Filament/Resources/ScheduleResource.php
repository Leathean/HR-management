<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TimeColumn;
use Filament\Facades\Filament;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\DateColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon; // Don't forget to import Carbon

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?int $navigationSort = 3;
    protected static ?String $navigationGroup = 'Records';
    protected static ?string $modelLabel = 'Employee Schedules';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('employees_id')
                ->label('Employee')
                ->options(\App\Models\Employee::all()->mapWithKeys(function ($employee) {
                    $middle = $employee->MNAME ? " {$employee->MNAME}" : '';
                    return [$employee->id => "{$employee->FNAME}{$middle} {$employee->LNAME}"];
                }))
                ->searchable()
                ->required(),

            TextInput::make('NAME')
                ->label('Schedule Name')
                ->required(),

            TimePicker::make('STARTTIME')
                ->label('Start Time')
                ->seconds(false) // optional, false by default
                ->format('H:i')
                ->required(),

            TimePicker::make('ENDTIME')
                ->label('End Time')
                ->seconds(false)
                ->format('H:i')
                ->required(),

            Select::make('SCHEDULE_TYPE')
                ->label('Schedule Type')
                ->options([
                    'WORKDAY' => 'Workday',
                    'ONLEAVE' => 'On Leave',
                    'RESTDAY' => 'On Rest Day',
                    'LEAVEPAY' => 'On Leave with Pay',
                ])
                ->required(),

            DatePicker::make('DATE')->label('Date')->required()->default(now()),
        ]);
    }

    public static function table(Table $table): Table
    {
        $user = Filament::auth()->user();
        return $table
            ->columns([
                TextColumn::make('employee_full_name')
                    ->label('Employee Name')
                    ->getStateUsing(fn($record) => $record->employee
                        ? "{$record->employee->FNAME} {$record->employee->MNAME} {$record->employee->LNAME}"
                        : 'N/A')
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('FNAME', 'like', "%{$search}%")
                                ->orWhere('MNAME', 'like', "%{$search}%")
                                ->orWhere('LNAME', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('NAME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STARTTIME'),
                Tables\Columns\TextColumn::make('ENDTIME'),
                Tables\Columns\TextColumn::make('SCHEDULE_TYPE'),
                Tables\Columns\TextColumn::make('DATE')
                    ->date()
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
                // Existing "My Schedule" filter
                ...(($user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN') ? [
                    Filter::make('My Schedule')
                        ->query(fn (Builder $query) => $query->where('employees_id', $user->employee?->id)),
                ] : []),

                // ---

                // New Date Filters
                Filter::make('this_day')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereDate('DATE', Carbon::today())
                    ),

                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereBetween('DATE', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ),

                Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereMonth('DATE', Carbon::now()->month)
                              ->whereYear('DATE', Carbon::now()->year)
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'view' => Pages\ViewSchedule::route('/{record}'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
