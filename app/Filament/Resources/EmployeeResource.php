<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Table;
use Filament\Resources\RelationManager;
use Illuminate\Support\Facades\Hash;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Personal Information Section
            Section::make('Personal Information')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        TextInput::make('FNAME')
                            ->required()
                            ->label('First Name')
                            ->maxLength(255),
                        TextInput::make('MNAME')
                            ->label('Middle Name')
                            ->maxLength(255),
                        TextInput::make('LNAME')
                            ->required()
                            ->label('Last Name')
                            ->maxLength(255),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        TextInput::make('EMAIL')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(fn (string $state): string => strtolower(trim($state))),
                        TextInput::make('PNUMBER')
                            ->label('Phone Number')
                            ->maxLength(11)
                            ->numeric()
                            ->default(null),
                    ]),
                ]),

            // Employment Details Section
            Section::make('Employment Details')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        DatePicker::make('EMPLOYMENT_START')
                            ->required(),
                        Select::make('ejob_id')
                            ->label('Job')
                            ->relationship('ejob', 'EJOB_NAME')
                            ->required(),
                        Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'DP_NAME')
                            ->required(),
                    ]),
                ]),

            // Salary Section
            Section::make('Salary')
                ->schema([
                    Checkbox::make('has_salary')
                        ->label('Enable Salary Input')
                        ->reactive(),
                    TextInput::make('SALARY')
                        ->label('Base Salary')
                        ->numeric()
                        ->visible(fn ($get) => $get('has_salary'))
                        ->required(fn ($get) => $get('has_salary')),
                ]),

            // System Access Section
            Section::make('System Access')
                ->schema([
                    Select::make('users_id')
                        ->label('Assigned User')
                        ->options(
                            fn () => User::whereNotIn('id', Employee::pluck('users_id'))->pluck('name', 'id')
                        )
                        ->searchable()
                        ->required()
                        ->disabled(fn (Forms\Get $get, Forms\Set $set, ?Employee $record) => filled($record)),
                ]),

            // User Account Section (only visible in edit/view)
            Section::make('User Account')
                ->relationship('user')  // Establish relationship to User
                ->schema([
                    TextInput::make('name')
                        ->label('User Name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label('User Email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    TextInput::make('password')
                        ->label('User Password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))  // Hash password on save
                        ->dehydrated(fn ($state) => filled($state))  // Only dehydrate if password is filled
                        ->maxLength(255)
                        ->minLength(8),
                        TextInput::make('ACCESS')
                        ->label('ACCESS CONTROL')
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))  // Hash password on save
                        ->dehydrated(fn ($state) => filled($state))  // Only dehydrate if password is filled
                        ->disabled(),
                ])
                ->visible(fn (string $operation): bool => in_array($operation, ['edit', 'view'])),  // Show only on edit and view modes
        ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers as needed
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('FNAME')->searchable(),
                Tables\Columns\TextColumn::make('MNAME')->searchable(),
                Tables\Columns\TextColumn::make('LNAME')->searchable(),
                Tables\Columns\TextColumn::make('EMAIL')->searchable(),
                Tables\Columns\TextColumn::make('EMPLOYMENT_START')->date()->sortable(),
                Tables\Columns\TextColumn::make('users_id')->searchable()                ->label('Employee Name')
                ->sortable()
                ->getStateUsing(function ($record) {

                    $user = $record->user;
                    return $user ? $user->name: 'N/A';
                }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),

        ];
    }
}
