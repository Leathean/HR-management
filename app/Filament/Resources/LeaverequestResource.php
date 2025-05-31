<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaverequestResource\Pages;
use App\Models\Leaverequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Filters\Filter;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Carbon\Carbon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Illuminate\Validation\ValidationException;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;

class LeaverequestResource extends Resource
{
    protected static ?string $model = Leaverequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'Leave Requests';
        protected static ?int $navigationSort = 2;
        protected static ?String $navigationGroup = 'Requests';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('employees_id')
                ->default(fn () => Filament::auth()->user()?->employee?->id)
                ->dehydrated(true)
                ->required(),
                DatePicker::make('LEAVEDATE')
                ->required()
                ->reactive()
                ->minDate(Carbon::today()),// Enable live updates
                DatePicker::make('RETURNDATE')
                ->required()
                ->reactive()
                ->minDate(Carbon::tomorrow())
                ->afterStateUpdated(function (callable $get, callable $set) {
                    $start = $get('LEAVEDATE');
                    $end = $get('RETURNDATE');
                    if ($start && $end) {
                        $startDate = \Carbon\Carbon::parse($start);
                        $endDate = \Carbon\Carbon::parse($end);
                        if ($endDate->lessThan($startDate)) {
                            $set('TOTAL_AMOUNT_LEAVE', null);
                        } else {
                            $days = $startDate->diffInDays($endDate);
                            $set('TOTAL_AMOUNT_LEAVE', $days);
                        }
                    } else {
                        $set('TOTAL_AMOUNT_LEAVE', null);
                    }
                }),

                Forms\Components\TextInput::make('TOTAL_AMOUNT_LEAVE')
                ->label('Total Leave Days')
                ->readonly() // user cannot edit
                ->dehydrated() // will be saved to database
                ->reactive(),

            Forms\Components\TextInput::make('REASONS')->maxLength(255),

            Forms\Components\Select::make('LEAVETYPE')
                ->options([
                    'NONE' => 'NONE',
                    'SICK LEAVE' => 'SICK LEAVE',
                    'PATERNITY' => 'PATERNITY',
                    'LEAVEPAY' => 'LEAVEPAY',

                ])
                ->required(),
            Forms\Components\TextInput::make('LEAVESTATUS')
                ->default('PENDING')
                ->disabled()
                ->dehydrated(),
        ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['employees_id'] = Filament::auth()->user()?->employee?->id;
        return $data;

    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make('Employee Details') // Custom title
                ->description('Information about the employee requesting leave.')
                ->schema([
                    TextEntry::make('employee.FNAME')
                        ->label('Employee Name')
                        ->formatStateUsing(fn($record) => $record->employee
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
                        ->label('Phone Number'),
                ])
                ->collapsible()
                ->persistCollapsed(),

            Section::make('Leave Information') // Custom title
                ->description('Details regarding the leave request.')
                ->schema([
                    TextEntry::make('LEAVEDATE')
                        ->label('Leave Date')
                        ->date(),
                    TextEntry::make('RETURNDATE')
                        ->label('Return Date')
                        ->date(),
                    TextEntry::make('TOTAL_AMOUNT_LEAVE')
                        ->label('Total Leave Days'),
                    TextEntry::make('REASONS')
                        ->label('Reasons'),
                    TextEntry::make('LEAVETYPE')
                        ->label('Leave Type'),
                    TextEntry::make('LEAVESTATUS')
                        ->label('Leave Status'),
                ])
                ->collapsible()
                ->persistCollapsed(),

            Section::make('Review Details') // Custom title
                ->description('Information about the review of the leave request.')
                ->schema([
                    TextEntry::make('approver.FNAME')
                        ->label('Reviewer name')
                        ->formatStateUsing(fn($record) => $record->approver
                            ? "{$record->approver->FNAME} {$record->approver->MNAME} {$record->approver->LNAME}"
                            : 'N/A'),
                    TextEntry::make('APPROVEDATE')
                        ->label('Date Reviewed')
                        ->date(),
                ])
                ->collapsible()
                ->persistCollapsed(),

                Section::make('Timestamps') // Custom title
                ->description('Dates when the leave request was created and updated.')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Date Created')
                        ->date(),
                    TextEntry::make('updated_at')
                        ->label('Date Updated')
                        ->date(),
                ])
                ->collapsible()
                ->persistCollapsed(),
        ]);
}

    public static function table(Table $table): Table
    {
        $user = Filament::auth()->user();
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employees_id')
                    ->label('Employee Name')
                    ->getStateUsing(fn($record) => $record->employee ? "{$record->employee->FNAME} {$record->employee->MNAME} {$record->employee->LNAME}" : 'N/A')
                    ->sortable()
                    ->extraAttributes(['class' => 'text-sm']),

                Tables\Columns\TextColumn::make('LEAVEDATE')->date()->sortable()->extraAttributes(['class' => 'text-sm'])->toggleable(),
                Tables\Columns\TextColumn::make('RETURNDATE')->date()->sortable()->extraAttributes(['class' => 'text-sm'])->toggleable(),
                Tables\Columns\TextColumn::make('TOTAL_AMOUNT_LEAVE')->label('Amount of leave')->extraAttributes(['class' => 'text-sm']),
                Tables\Columns\TextColumn::make('LEAVETYPE'),
                Tables\Columns\TextColumn::make('LEAVESTATUS'),
                Tables\Columns\TextColumn::make('APPROVEDATE')->date()->sortable()->extraAttributes(['class' => 'text-sm'])->toggleable(),
                Tables\Columns\TextColumn::make('approver_id')
                ->label('Reviewed BY')
                ->getStateUsing(fn($record) => $record->approver ? "{$record->approver->FNAME} {$record->approver->MNAME} {$record->approver->LNAME}" : 'N/A')
                ->sortable()
                ->toggleable()
                ->extraAttributes(['class' => 'text-sm']),

                Tables\Columns\TextColumn::make('created_at')->date()->sortable()->label('DATE CREATED')->extraAttributes(['class' => 'text-sm'])->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->date()->sortable()->label('DATE UPDATED')->extraAttributes(['class' => 'text-sm'])->toggleable(),
            ])
            ->filters([
    ...(($user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN') ? [
        Filter::make('My Requests')
            ->query(fn (Builder $query) => $query->where('employees_id', $user->employee?->id)),
    ] : []),

    // Leave Type Filters
    ...(($user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN') ? [
        // HR/Admin see all leave types (no employee restriction)
        Filter::make('NONE')
            ->query(fn (Builder $query) => $query->where('LEAVETYPE', 'NONE')),

        Filter::make('SICK LEAVE')
            ->query(fn (Builder $query) => $query->where('LEAVETYPE', 'SICK LEAVE')),

        Filter::make('PATERNITY')
            ->query(fn (Builder $query) => $query->where('LEAVETYPE', 'PATERNITY')),

        Filter::make('LEAVEPAY')
            ->query(fn (Builder $query) => $query->where('LEAVETYPE', 'LEAVEPAY')),
    ] : [
        // Regular users: same filters, but scoped to their own requests
        Filter::make('NONE')
            ->query(fn (Builder $query) => $query
                ->where('employees_id', $user->employee?->id)
                ->where('LEAVETYPE', 'NONE')),

        Filter::make('SICK LEAVE')
            ->query(fn (Builder $query) => $query
                ->where('employees_id', $user->employee?->id)
                ->where('LEAVETYPE', 'SICK LEAVE')),

        Filter::make('PATERNITY')
            ->query(fn (Builder $query) => $query
                ->where('employees_id', $user->employee?->id)
                ->where('LEAVETYPE', 'PATERNITY')),

        Filter::make('LEAVEPAY')
            ->query(fn (Builder $query) => $query
                ->where('employees_id', $user->employee?->id)
                ->where('LEAVETYPE', 'LEAVEPAY')),
    ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                        // THIS IS ACTION METHOD TO UPDATE THE STATUS OF LEAVE
                Action::make('updateLeaveStatus')
                        ->label('Review')
                        ->icon('heroicon-o-pencil')
                        ->visible(function ($record) {
                            $user = Filament::auth()->user();
                            return $record->LEAVESTATUS === 'PENDING' && in_array($user->ACCESS, ['HR', 'ADMIN']);
                        })
                        ->form([
                            Select::make('LEAVESTATUS')
                                ->label('Leave Status')
                                ->options([
                                    'ACCEPTED' => 'Accepted',
                                    'DENY' => 'Denied',
                                ])
                                ->required(),
                        ])
                        ->requiresConfirmation()
                        ->action(function (array $data, $record) {
                            $record->LEAVESTATUS = $data['LEAVESTATUS'];
                            $record->approver_id = Filament::auth()->user()?->employee?->id;
                            $record->APPROVEDATE = date(now());
                            $record->save();

                            Notification::make()
                                ->title('Leave Status Reviewed Successfully')
                                ->success()
                                ->send();
                        }),
            ])
            ;
    }
    protected function handleRecordCreation(array $data): Leaverequest
    {
        Log::info('Create form submitted with data:', $data);
        return Leaverequest::create($data);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaverequests::route('/'),
            'create' => Pages\CreateLeaverequest::route('/create'),
            'view' => Pages\ViewLeaverequest::route('/{record}'),
            'edit' => Pages\EditLeaverequest::route('/{record}/edit'),
        ];
    }


    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
