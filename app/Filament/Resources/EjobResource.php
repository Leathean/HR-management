<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EjobResource\Pages;
use App\Filament\Resources\EjobResource\RelationManagers;
use App\Models\Ejob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EjobResource extends Resource
{
    protected static ?string $model = Ejob::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    protected static ?String $navigationGroup = 'Job';
    protected static ?string $navigationLabel = 'Job Descriptions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('EJOB_NAME')->LABEL('JOB NAME')->unique()->required(),
                Forms\Components\TextInput::make('EJOB_DESCRIPTION')->LABEL('JOB DESCRIPTIONS')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('EJOB_NAME')->LABEL('JOB NAME'),
                TextColumn::make('EJOB_DESCRIPTION')->LABEL('JOB DESCRIPTIONS'),
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
            'index' => Pages\ListEjobs::route('/'),
            'create' => Pages\CreateEjob::route('/create'),
            'view' => Pages\ViewEjob::route('/{record}'),
            'edit' => Pages\EditEjob::route('/{record}/edit'),
        ];
    }
}
