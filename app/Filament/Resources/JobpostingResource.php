<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobpostingResource\Pages;
use App\Filament\Resources\JobpostingResource\RelationManagers;
use App\Models\Jobposting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextAREA;
class JobpostingResource extends Resource
{
    protected static ?string $model = Jobposting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    protected static ?String $navigationGroup = 'Job';
    protected static ?string $navigationLabel = 'Job Posting';
    public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([
            Grid::make(2)->schema([
                Select::make('ejobs_id')
                    ->label('Ejob')
                    ->relationship('ejob', 'EJOB_NAME')
                    ->required(),

                Select::make('departments_id')
                    ->label('Department')
                    ->relationship('department', 'DP_NAME')
                    ->required(),

                DatePicker::make('POSTED_DATE')
                    ->label('Posted Date')
                    ->required(),

                Textarea::make('QUALIFICATION')
                    ->label('Qualification')
                    ->required()
                    ->columnSpanFull(), // Optional: makes it span full width

            ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ejob.EJOB_NAME')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.DP_NAME')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('POSTED_DATE')
                    ->date()
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
            'index' => Pages\ListJobpostings::route('/'),
            'create' => Pages\CreateJobposting::route('/create'),
            'view' => Pages\ViewJobposting::route('/{record}'),
            'edit' => Pages\EditJobposting::route('/{record}/edit'),
        ];
    }
}
