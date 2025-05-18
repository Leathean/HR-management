<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobApplicationResource\Pages;
use App\Filament\Resources\JobApplicationResource\RelationManagers;
use App\Models\JobApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\TextColumn;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    protected static ?String $navigationGroup = 'Job';
    protected static ?string $navigationLabel = 'Applicants';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->with(['jobPosting.ejob']); // eager load the nested relationship
}
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('jobPosting.ejob.EJOB_NAME')
                ->label('Job Title')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('jobPosting.department.DP_NAME')
                ->label('Job Title')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('FNAME'),

            Tables\Columns\TextColumn::make('MNAME'),
            Tables\Columns\TextColumn::make('LNAME'),
            Tables\Columns\TextColumn::make('created_at')->date(),
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
            'index' => Pages\ListJobApplications::route('/'),
            'create' => Pages\CreateJobApplication::route('/create'),
            'view' => Pages\ViewJobApplication::route('/{record}'),
            'edit' => Pages\EditJobApplication::route('/{record}/edit'),
        ];
    }
}
