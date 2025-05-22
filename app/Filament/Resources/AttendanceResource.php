<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Records';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('employees_id')
                ->default(fn () => Filament::auth()->user()?->employee?->id)
                ->dehydrated(true)
                ->required(),

            Forms\Components\DatePicker::make('date')
                ->default(now())
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $user = Filament::auth()->user();

        return $table
            ->columns([
                TextColumn::make('employee_full_name')
                    ->label('Employee Name')
                    ->getStateUsing(fn ($record) => $record->employee
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

                TextColumn::make('date')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d-M-Y')),

                TextColumn::make('time_in')->label('Time In'),
                TextColumn::make('time_out')->label('Time Out'),

                BadgeColumn::make('time_in_status')
                    ->label('Time In Status')
                    ->colors([
                        'success' => 'ON TIME',
                        'danger' => 'LATE',
                    ]),

                BadgeColumn::make('time_out_status')
                    ->label('Time Out Status')
                    ->colors([
                        'success' => 'ON TIME',
                        'warning' => 'EARLY OUT',
                    ]),

                BadgeColumn::make('status_day')
                    ->label('Overall Status')
                    ->colors([
                        'success' => 'PRESENT',
                        'danger' => 'ABSENT',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status_day')
                    ->label('Overall Status')
                    ->options([
                        'PRESENT' => 'Present',
                        'ABSENT' => 'Absent',
                    ]),
                ...(($user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN') ? [
                    SelectFilter::make('my_attendance')
                        ->label('My Attendance')
                        ->query(fn (Builder $query) => $query->where('employees_id', $user->employee?->id)),
                ] : []),
            ])
            ->actions([
                Tables\Actions\Action::make('Time In')
                    ->action(fn ($record) => $record->update(['time_in' => now()->format('H:i:s')]))
                    ->hidden(fn ($record) => filled($record->time_in)),

                Tables\Actions\Action::make('Time Out')
                    ->action(fn ($record) => $record->update(['time_out' => now()->format('H:i:s')]))
                    ->hidden(fn ($record) => empty($record->time_in) || filled($record->time_out)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
