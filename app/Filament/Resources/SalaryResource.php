<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryResource\Pages;
use App\Filament\Resources\SalaryResource\RelationManagers;
use App\Models\Salary;
use App\Models\EmployeeBenefit;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Button;
use Filament\Tables\Actions\Action;
use App\Actions\ResetStars;

class SalaryResource extends Resource
{
    protected static ?string $model = Salary::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
        protected static ?int $navigationSort = 3;
        protected static ?String $navigationGroup = 'Finances';
        protected static ?string $modelLabel = 'Employee Salaries';

public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Section::make('Select Employee')
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
            ])
            ->columns(1),

        Forms\Components\Section::make('Employee Benefits')
            ->schema([
                Placeholder::make('employee_benefits')
                    ->label('Active Benefits')
                    ->content(function ($get) {
                        $employeeId = $get('employees_id');
                        if (!$employeeId) {
                            return new HtmlString('No employee selected.');
                        }

                        $benefits = EmployeeBenefit::with('benefit')
                            ->where('employees_id', $employeeId)
                            ->where('STATUS', true)
                            ->get();

                        if ($benefits->isEmpty()) {
                            return new HtmlString('No active benefits.');
                        }

                        $list = $benefits->map(function ($benefit) {
                            return "- {$benefit->benefit->NAME} ({$benefit->AMOUNT})";
                        })->implode('<br>');

                        return new HtmlString($list);
                    })
                    ->visible(fn ($get) => $get('employees_id')),
            ])
            ->columns(1),

        Forms\Components\Section::make('Salary Details')
            ->schema([
                TextInput::make('BASICSALARY')
                    ->label('Basic Salary')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $employeeId = $get('employees_id');
                        $benefitsTotal = 0;

                        if ($employeeId) {
                            $benefitsTotal = EmployeeBenefit::where('employees_id', $employeeId)
                                ->where('STATUS', true)
                                ->sum('AMOUNT');
                        }

                        $net = $state - $benefitsTotal;
                        $set('NETSALARY', $net > 0 ? $net : 0);
                    }),

                TextInput::make('NETSALARY')
                    ->label('Net Salary')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),

            ])
            ->columns(2),
    ]);
}



    public static function table(Table $table): Table
    {
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
        Tables\Columns\TextColumn::make('BASICSALARY')
                ->label('Basic Salary')
                ->money('PHP') // Optional formatting
                ->sortable(),

            // Total Benefits (dynamic, calculated via relationship or accessor)
            Tables\Columns\TextColumn::make('employee.total_benefits')
                ->label('Total Benefits')
                ->money('PHP') // Assumes you format this in an accessor
                ->sortable(),

            // Net Salary
            Tables\Columns\TextColumn::make('NETSALARY')
                ->label('Net Salary')
                ->money('PHP')
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Updated')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                 Tables\Actions\Action::make('recalculate_salary')
                    ->label('Recalculate')
                    ->icon('heroicon-m-arrow-path') // Updated icon
                    ->requiresConfirmation()
                    ->action(function (Salary $record): void {
                        $employeeId = $record->employees_id;
                        $basicSalary = $record->BASICSALARY;
                        $benefitsTotal = 0;

                        if ($employeeId) {
                            $benefitsTotal = \App\Models\EmployeeBenefit::where('employees_id', $employeeId)
                                ->where('STATUS', true)
                                ->sum('AMOUNT');
                        }

                        $netSalary = $basicSalary - $benefitsTotal;

                        $record->NETSALARY = $netSalary > 0 ? $netSalary : 0;
                        $record->save();
                    })
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
            'view' => Pages\ViewSalary::route('/{record}'),
            'edit' => Pages\EditSalary::route('/{record}/edit'),
        ];
    }
}
