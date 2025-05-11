<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
        protected static ?int $navigationSort = 3;
        protected static ?String $navigationGroup = 'Records';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            TextColumn::make('employees_id')
                ->label('Employee')
                ->formatStateUsing(fn ($state, $record) =>
                    $record->employee?->FNAME . ' ' .
                    $record->employee?->MNAME . ' ' .
                    $record->employee?->LNAME
                )
                ->sortable()
                ->searchable(),
            TextColumn::make('date')->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d-M-Y')),
            TextColumn::make('time_in'),
            TextColumn::make('time_out'),
            TextColumn::make('status')
                ->label('Status')
                ->colors([
                    'success' => 'on time',
                    'danger' => 'late',
                    'secondary' => 'absent',
                ]),
        ])
            ->filters([
                // Only show this filter if user is HR or ADMIN
                ...(($user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN') ? [
                    Tables\Filters\Filter::make('MY Attendance')
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
        return [
            //
        ];
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
