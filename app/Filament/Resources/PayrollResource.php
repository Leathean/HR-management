<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Models\Payroll;
use App\Models\Salary;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Builder;
use App\Services\PayrollService;
use Filament\Tables\Columns\TextColumn;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Payroll Management';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee Information')
                    ->schema([
                        Select::make('employees_id')
                            ->label('Employee')
                            ->options(Employee::all()->mapWithKeys(function ($employee) {
                                $middle = $employee->MNAME ? " {$employee->MNAME}" : '';
                                return [$employee->id => "{$employee->FNAME}{$middle} {$employee->LNAME}"];
                            })->toArray())
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if (!$state) {
                                    $set('salary_id', null);
                                    $set('basic_salary_display', null);
                                    $set('gross_salary', 0);
                                    $set('total_deductions', 0);
                                    $set('net_salary', 0);
                                    return;
                                }

                                $salary = Salary::where('employees_id', $state)->where('STATUS', true)->first();
                                $salaryId = $salary?->id;

                                $set('salary_id', $salaryId);
                                $set('basic_salary_display', number_format($salary?->BASICSALARY ?? 0, 2));

                                static::recalculatePayroll($set, $get);
                            }),

                        Hidden::make('salary_id')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::recalculatePayroll($set, $get);
                            }),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('Payroll Period')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->reactive()
                            ->displayFormat('d F Y')
                            ->afterStateUpdated(function (callable $set, callable $get, ?string $state) {
                                if ($state) {
                                    $endDate = Carbon::parse($state)->addDays(14)->toDateString();
                                    $set('end_date', $endDate);

                                    $employeeId = $get('employees_id');
                                    $salaryId = $get('salary_id');

                                    if ($employeeId && $salaryId) {
                                        $service = app(PayrollService::class);
                                        $result = $service->calculatePayroll($employeeId, $salaryId, $state, $endDate);

                                        $set('gross_salary', $result['gross_salary']);
                                        $set('total_deductions', $result['total_deductions']);
                                        $set('net_salary', $result['net_salary']);
                                    }
                                } else {
                                    $set('end_date', null);
                                }
                            }),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->displayFormat('d F Y')
                            ->disabled()
                            ->reactive()
                            ->dehydrated(),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('Salary Information')
                    ->schema([
                        TextInput::make('basic_salary_display')
                            ->label('Basic Salary')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (callable $get) => filled($get('employees_id')))
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record?->salary) {
                                    $component->state(number_format($record->salary->BASICSALARY, 2));
                                }
                            }),

                        TextInput::make('gross_salary')
                            ->label('Gross Salary')
                            ->numeric()
                            ->readonly()
                            ->default(0),

                        TextInput::make('total_deductions')
                            ->label('Total Deductions')
                            ->numeric()
                            ->readonly()
                            ->default(0),

                        TextInput::make('net_salary')
                            ->label('Net Salary')
                            ->numeric()
                            ->readonly()
                            ->default(0),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    protected static function recalculatePayroll(callable $set, callable $get): void
    {
        $employeeId = $get('employees_id');
        $salaryId = $get('salary_id');
        $startDate = $get('start_date');
        $endDate = $get('end_date');

        if ($employeeId && $salaryId && $startDate && $endDate) {
            $service = app(PayrollService::class);
            $result = $service->calculatePayroll($employeeId, $salaryId, $startDate, $endDate);

            $set('gross_salary', $result['gross_salary']);
            $set('total_deductions', $result['total_deductions']);
            $set('net_salary', $result['net_salary']);
        } else {
            $set('gross_salary', 0);
            $set('total_deductions', 0);
            $set('net_salary', 0);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_full_name')
                    ->label('Employee Name')
                    ->getStateUsing(fn($record) => $record->employee
                        ? trim("{$record->employee->FNAME} {$record->employee->MNAME} {$record->employee->LNAME}")
                        : 'N/A')
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('FNAME', 'like', "%{$search}%")
                                ->orWhere('MNAME', 'like', "%{$search}%")
                                ->orWhere('LNAME', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('salary_amount')
                    ->label('Salary Amount')
                    ->getStateUsing(fn($record) => $record->salary
                        ? number_format($record->salary->BASICSALARY, 2)
                        : '0.00')
                    ->money('php')
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('gross_salary')
                    ->label('Gross Salary')
                    ->money('php')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_deductions')
                    ->label('Total Deductions')
                    ->money('php')
                    ->sortable(),

                TextColumn::make('net_salary')
                    ->label('Net Salary')
                    ->money('php')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'view' => Pages\ViewPayroll::route('/{record}'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
