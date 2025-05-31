<?php

namespace App\Filament\Resources\BenefitResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employee';

    protected static ?string $recordTitleAttribute = 'FNAME'; // Adjust this to the employee's name column

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_benefits.AMOUNT') // Display the AMOUNT from the pivot table
                    ->label('Benefit Amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('employee_benefits.STATUS') // Display the STATUS from the pivot table
                    ->label('Benefit Status')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('FNAME')->searchable(), // Adjust this to the employee's name column
                Tables\Columns\TextColumn::make('LNAME')->searchable(),
                Tables\Columns\TextColumn::make('EMAIL')->searchable(),
                Tables\Columns\TextColumn::make('employeebenefit.AMOUNT')->label('Benefit Amount'), // Display the AMOUNT from the pivot table
                Tables\Columns\IconColumn::make('employeebenefit.STATUS') // Display the STATUS from the pivot table
                    ->label('Benefit Status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()  // Use AttachAction instead of CreateAction
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
