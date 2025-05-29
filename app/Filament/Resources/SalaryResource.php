<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryResource\Pages;
use App\Filament\Resources\SalaryResource\RelationManagers;
use App\Models\Salary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
class SalaryResource extends Resource
{
    protected static ?string $model = Salary::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('employees_id')
                    ->label('Employee')
                    ->options(\App\Models\Employee::all()->mapWithKeys(function ($employee) {
                        $middle = $employee->MNAME ? " {$employee->MNAME}" : '';
                        return [$employee->id => "{$employee->FNAME}{$middle} {$employee->LNAME}"];
                    }))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('BASICSALARY', 0); // Reset when employee changes
                        $set('NETSALARY', 0);
                    }),

            Forms\Components\TextInput::make('BASICSALARY')
                ->label('Basic Salary')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('SALARY_TYPE')
                ->options([
                    'MONTHLY' => 'Monthly',
                    'DAILY' => 'Daily',
                    'BIWEEKLY' => 'Bi-weekly',
                ])
                ->required(),

            Forms\Components\Toggle::make('STATUS')
                ->label('Active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('employee_full_name')
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
            Tables\Columns\TextColumn::make('BASICSALARY')->money(),
            Tables\Columns\TextColumn::make('SALARY_TYPE'),
            Tables\Columns\IconColumn::make('STATUS')->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),


            Tables\Actions\Action::make('activate')
                ->label('Activate')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['STATUS' => true]))
                ->visible(fn ($record) => !$record->STATUS),

            Tables\Actions\Action::make('deactivate')
                ->label('Deactivate')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['STATUS' => false]))
                ->visible(fn ($record) => $record->STATUS),
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
            'index' => Pages\ListSalaries::route('/'),
            'create' => Pages\CreateSalary::route('/create'),
            // 'view' => Pages\ViewSalary::route('/{record}'),
            'edit' => Pages\EditSalary::route('/{record}/edit'),
        ];
    }
}
