<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Filament\Resources\EvaluationResource\RelationManagers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Evaluation;
use App\Models\Questionnaire;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Mokhosh\FilamentRating\Components\Rating;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
        protected static ?int $navigationSort = 3;
        protected static ?String $navigationGroup = 'Evaluations';
        protected static ?string $modelLabel = 'Evaluation Form';
 public static function form(Form $form): Form
    {
        $questions = Questionnaire::active()->get();

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

                Section::make('Evaluation Questions')
                    ->schema([
                        Tabs::make('QUESTION')
                            ->tabs([
                                Tabs\Tab::make('Rating Questions')
                                    ->schema(
                                        $questions->map(fn($question) => RATING::make("RATINGS.{$question->id}")
                                            ->label($question->QUESTION)
                                            ->required()
                                            ->hint($question->hint ?? null)
                                            ->extraAttributes(['class' => 'mb-4'])
                                        )->toArray()
                                    ),

                                Tabs\Tab::make('Additional Feedback')
                                    ->schema([
                                        Textarea::make('COMMENT')
                                            ->label('Your Comments/Suggestions')
                                            ->maxLength(1000)
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
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


                TextColumn::make('average_rating')
                    ->label('Avg Rating')
                    ->numeric(decimalPlaces: 1),
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
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'view' => Pages\ViewEvaluation::route('/{record}'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}
