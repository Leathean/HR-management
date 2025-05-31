<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryResource\Pages;
use App\Models\Salary;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Filament\Resources\SalaryResource\Pages\CreateSalary;
use App\Filament\Resources\SalaryResource\Pages\EditSalary;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Infolists\Infolist;
use Carbon\Carbon;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;

class SalaryResource extends Resource
{
    protected static ?string $model = Salary::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Employee Information')
                ->schema([
                    CheckboxList::make('employees_id')
                        ->label('Select one or more employees')
                        ->options(
                            Employee::whereDoesntHave('salary')
                                ->get()
                                ->mapWithKeys(fn ($e) => [$e->id => "{$e->FNAME} {$e->MNAME} {$e->LNAME}"])
                                ->toArray()
                        )
                        ->columns(2)
                        ->required()
                        ->visible(fn ($livewire) => $livewire instanceof CreateSalary),

                    Placeholder::make('employee_name')
                        ->label('Employee Name')
                        ->content(fn (?Salary $record) => $record && $record->employee
                            ? "{$record->employee->FNAME} " .
                                ($record->employee->MNAME ? "{$record->employee->MNAME} " : '') .
                                "{$record->employee->LNAME}"
                            : 'N/A')
                        ->visible(fn ($livewire) => $livewire instanceof EditSalary),
                ])
                ->collapsible(),

            Section::make('Salary Details')
                ->schema([
                    TextInput::make('BASICSALARY')
                                ->label('Basic Salary')
                                ->numeric()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $daysInMonth = Carbon::now()->daysInMonth;
                                    if ($daysInMonth > 0) {
                                        $perDayRate = $state / $daysInMonth;
                                        $set('PERDAYRATE', round($perDayRate, 2));
                                    }
                                }),

                            TextInput::make('PERDAYRATE')
                                ->label('Per Day Rate')
                                ->numeric()
                                ->disabled()              // Make it read-only
                                ->dehydrated(true)       // Prevent saving unless needed
                                ->required()
                                ->afterStateHydrated(function ($state, callable $set, $get) {
                                    $basicSalary = $get('BASICSALARY');
                                    $daysInMonth = Carbon::now()->daysInMonth;
                                    if ($basicSalary && $daysInMonth > 0) {
                                        $set('PERDAYRATE', round($basicSalary / $daysInMonth, 2));
                                    }
                                                }),


                ])
                ->collapsible(),

            Section::make('Status')
                ->schema([
                    Toggle::make('STATUS')
                        ->label('Active')
                        ->default(true),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist // for view so it shows all related information for the employee

    {
        return $infolist
            ->schema([
                InfolistSection::make('Employee Details')
                    ->description('Information about the employee.')
                    ->schema([
                        TextEntry::make('employee.FNAME')
                            ->label('Employee Name')
                            ->formatStateUsing(fn ($record) => $record->employee
                                ? "{$record->employee->FNAME} {$record->employee->MNAME} {$record->employee->LNAME}"
                                : 'N/A'),
                        TextEntry::make('employees_id')
                            ->label('Employee ID'),

                        TextEntry::make('employee.ejob.EJOB_NAME')
                            ->label('Job')
                            ->formatStateUsing(fn ($state, $record) => $record->employee
                                ? "{$record->employee->ejob->EJOB_NAME} - {$record->employee->ejob->EJOB_DESCRIPTION}"
                                : 'N/A'),
                        TextEntry::make('employee.department')
                            ->label('Department')
                            ->formatStateUsing(fn ($state, $record) => $record->employee
                                ? "{$record->employee->department->DP_NAME} - {$record->employee->department->DP_DESCRIPTION}"
                                : 'N/A'),
                        TextEntry::make('employee.PNUMBER')
                            ->label('Phone Number')
                    ])
                    ->collapsible()
                    ->collapsed(false),

                InfolistSection::make('Salary Details')
                    ->description('Details about the employee salary.')
                    ->schema([
                        TextEntry::make('BASICSALARY')
                            ->label('Basic Salary')
                            ->formatStateUsing(fn ($state) => number_format($state, 2)),

                        TextEntry::make('PERDAYRATE')
                            ->label('Rate per Day')
                            ->formatStateUsing(fn ($state) => number_format($state, 2)),


                        TextEntry::make('STATUS')
                            ->label('Status')
                            ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive'),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                InfolistSection::make('Timestamps')
                    ->description('Record creation and update timestamps.')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Date Created')
                            ->formatStateUsing(fn ($state) => optional($state)->format('Y-m-d')),

                        TextEntry::make('updated_at')
                            ->label('Date Updated')
                            ->formatStateUsing(fn ($state) => optional($state)->format('Y-m-d')),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('employee_full_name')
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
            Tables\Columns\TextColumn::make('BASICSALARY')->money('php')->label('Basic salary'),
            Tables\Columns\TextColumn::make('PERDAYRATE')->label(' Per day rate'),
            Tables\Columns\IconColumn::make('STATUS')->boolean()->label('Salary Status'),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('Enable Salary') // changes salary to enable when press isntead of going to edit
                ->label('Enable Salary')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['STATUS' => true]))
                ->visible(fn ($record) => !$record->STATUS),

            Tables\Actions\Action::make('Disable Salary') // disabled salary to enable when press isntead of going to edit
                ->label('Disable Salary')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalaries::route('/'),
            'create' => Pages\CreateSalary::route('/create'),
            'edit' => Pages\EditSalary::route('/{record}/edit'),
        ];
    }
}
