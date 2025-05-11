<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
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
        protected static ?int $navigationSort = 1;
        protected static ?String $navigationGroup = 'User management';

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

                Section::make('User Account')
                ->schema([
                        Select::make('users_id')
                            ->label('Assigned User')
                            ->options(function (Forms\Get $get, ?Employee $record) {
                                $assignedUserIds = Employee::when($record, fn ($q) => $q->where('id', '!=', $record->id))->pluck('users_id');

                                return User::whereNotIn('id', $assignedUserIds)
                                    ->orWhere('id', $record?->users_id) // Include the currently assigned user if editing
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->nullable()
                        ]),

            // User Account Section (only visible in edit/view)
            Section::make('User Account Details')
                ->relationship('user')
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
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->maxLength(255)
                        ->minLength(8),
                    TextInput::make('ACCESS')
                        ->label('ACCESS CONTROL')
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->disabled(),
                ])
                ->visible(function ($livewire, string $operation) {
                if (!in_array($operation, ['edit', 'view'])) {
                return false;
                }

                // Only try to access getRecord if available
                if (method_exists($livewire, 'getRecord')) {
                    $record = $livewire->getRecord();
                    return $record && $record->user;
                }

    return false;
                }),
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
                Tables\Columns\TextColumn::make('users_id')->searchable()->label('User Name')
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
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
