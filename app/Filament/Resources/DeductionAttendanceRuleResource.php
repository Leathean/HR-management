<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeductionAttendanceRuleResource\Pages;
use App\Filament\Resources\DeductionAttendanceRuleResource\RelationManagers;
use App\Models\DeductionAttendanceRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeductionAttendanceRuleResource extends Resource
{
    protected static ?string $model = DeductionAttendanceRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus-circle';
    protected static ?string $navigationGroup = 'Payroll Management';
    protected static ?string $modelLabel = 'Attendance Deduction Rule';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                   Forms\Components\Select::make('ATTENDANCE_TYPE')
                        ->label('Attendance Type')
                        ->options([
                            'LATE' => 'Late',
                            'ABSENT' => 'Absent',
                            'EARLY OUT' => 'Early Out',
                        ])
                        ->required()
                        ->unique(
                            ignoreRecord: true, // ✅ This makes it work for edit mode too
                        ),

                    Forms\Components\Select::make('DEDUCTION_METHOD')
                        ->label('Deduction Method')
                        ->options([
                            'FIXED' => 'Fixed Amount',
                            'PERCENTAGE' => 'Percentage',
                        ])
                        ->required()
                        ->reactive(),

                    Forms\Components\TextInput::make('DEDUCTION_ATTENDANCE_AMOUNT')
                        ->label('Deduction Value')
                        ->numeric()
                        ->required()
                        ->prefix(fn (callable $get) =>
                            $get('DEDUCTION_METHOD') === 'PERCENTAGE' ? '%' : '₱'
                        ),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ATTENDANCE_TYPE')->label('Attendance Type'),
                Tables\Columns\TextColumn::make('DEDUCTION_METHOD')->label('Deduction Type'),
                Tables\Columns\TextColumn::make('DEDUCTION_ATTENDANCE_AMOUNT')->label('Amount Deduction')
                    ->numeric()
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
                //
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
            'index' => Pages\ListDeductionAttendanceRules::route('/'),
            'create' => Pages\CreateDeductionAttendanceRule::route('/create'),
            'view' => Pages\ViewDeductionAttendanceRule::route('/{record}'),
            'edit' => Pages\EditDeductionAttendanceRule::route('/{record}/edit'),
        ];
    }
}
