<?php


namespace App\Filament\Resources;


use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\RelationManagers;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Salary;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Facades\Filament;
use Carbon\Carbon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists;
use Illuminate\Validation\ValidationException;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;


    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
        protected static ?int $navigationSort = 4;
        protected static ?String $navigationGroup = 'Finances';

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
                    ->required(),




            Select::make('salaries_id')
                ->label('Net Salary')
                ->options(function (callable $get) {
                    $employeeId = $get('employees_id');
                    if (!$employeeId) return [];


                    return \App\Models\Salary::where('employees_id', $employeeId)
                        ->orderByDesc('created_at')
                        ->pluck('NETSALARY', 'id');
                })
                ->required()
                ->searchable()
                ->hint('Select the net salary for the employee'),




                Select::make('STATUS')
                    ->options([
                        'PENDING' => 'Pending',
                        'PROCESSED' => 'Processed',
                    ])
                    ->required(),




                DatePicker::make('PAYDATE')
                    ->label('Pay Date')
                    ->required(),
            ]);
    }


public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make('Employee Details')
                ->description('Information about the employee.')
                ->schema([
                    TextEntry::make('employee.FNAME')
                        ->label('Employee First Name')
                        ->formatStateUsing(fn($record) => $record->employee ? $record->employee->FNAME : 'N/A'),
                    TextEntry::make('employee.LNAME')
                        ->label('Employee Last Name')
                        ->formatStateUsing(fn($record) => $record->employee ? $record->employee->LNAME : 'N/A'),
                    TextEntry::make('salary.BASICSALARY')
                        ->label('Basic Salary')
                        ->formatStateUsing(fn($record) => $record->salary ? $record->salary->BASICSALARY : 'N/A'),
                    TextEntry::make('salary.NETSALARY')
                        ->label('Net Salary')
                        ->formatStateUsing(fn($record) => $record->salary ? $record->salary->NETSALARY : 'N/A'),
                ])
                ->collapsible()
                ->persistCollapsed(),


                Section::make('Employee Benefits')
                    ->schema([
                        // Repeatable list if benefits exist
                        RepeatableEntry::make('employee.employeebenefit')
                            ->label('Benefits')
                            ->schema([
                                TextEntry::make('benefit.NAME')->label('Benefit'),
                                TextEntry::make('AMOUNT')->label('Amount'),
                            ])
                            ->columns(2)
                            ->visible(fn ($record) => $record->employee?->employeebenefit?->isNotEmpty() ?? false),

                        // Fallback message if no benefits exist
                        TextEntry::make('no_benefits')
                            ->label('')
                            ->default('No benefits assigned.')
                            ->visible(fn ($record) => $record->employee?->employeebenefit?->isEmpty() ?? true),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

            Section::make('Payroll Details')
                ->description('Details of the payroll status.')
                ->schema([
                    TextEntry::make('STATUS')
                        ->label('Payroll Status')
                        ->formatStateUsing(fn($record) => $record->STATUS),
                    TextEntry::make('PAYDATE')
                        ->label('Payroll Date')
                        ->date(),
                ])
                ->collapsible()
                ->persistCollapsed(),

            Section::make('Timestamps')
                ->description('Dates when the payroll was created and updated.')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Created At')
                        ->date(),
                    TextEntry::make('updated_at')
                        ->label('Updated At')
                        ->date(),
                ])
                ->collapsible()
                ->persistCollapsed(),
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



                Tables\Columns\TextColumn::make('PAYDATE')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('STATUS'),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

            Action::make('updateStatus')
                ->label('Update Status')
                ->action(function ($record) {
                    $record->update([
                        'STATUS' => 'PROCESSED',
                    ]);
                })
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check')
                ->visible(fn ($record) => $record->STATUS !== 'PROCESSED'),

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

        ];
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





