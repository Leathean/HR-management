<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeebenefitResource\Pages;
use App\Filament\Resources\EmployeebenefitResource\RelationManagers;
use App\Models\Employeebenefit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Benefit;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class EmployeebenefitResource extends Resource
{
    protected static ?string $model = Employeebenefit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
        protected static ?int $navigationSort = 3;
        protected static ?String $navigationGroup = 'Finances';
        protected static ?string $modelLabel = 'Employee Benefits';
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
                ->searchable()
                ->required(),

            Select::make('benefits_id')
                ->label('Benefit')
                ->options(function (Get $get) {
                    $empId = $get('employees_id');

                    if (!$empId) {
                        return \App\Models\Benefit::pluck('NAME', 'id');
                    }

                    $assignedBenefitIds = \App\Models\EmployeeBenefit::where('employees_id', $empId)->pluck('benefits_id');

                    return \App\Models\Benefit::whereNotIn('id', $assignedBenefitIds)->pluck('NAME', 'id');
                })


                ->searchable()
                ->required(),

                            TextInput::make('AMOUNT')
                                ->label('Amount')
                                ->numeric()
                                ->required()
                                ->minValue(0),

                            Toggle::make('STATUS')
                                ->label('Active')
                                ->inline(false),

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
                Tables\Columns\TextColumn::make('benefit.NAME')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('AMOUNT')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('STATUS')
                    ->boolean(),
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
            SelectFilter::make('benefits_id')
                ->label('Benefit')
                ->options(Benefit::pluck('NAME', 'id')->toArray())
                ->searchable(),

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
            'index' => Pages\ListEmployeebenefits::route('/'),
            'create' => Pages\CreateEmployeebenefit::route('/create'),
            'view' => Pages\ViewEmployeebenefit::route('/{record}'),
            'edit' => Pages\EditEmployeebenefit::route('/{record}/edit'),
        ];
    }
}
