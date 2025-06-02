<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayslipResource\Pages;
use App\Models\Payslip;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PayslipResource extends Resource
{
    protected static ?string $model = Payslip::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Payroll Management';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Payslip Approval')
                    ->schema([
                        Select::make('payroll_id')
                            ->label('Payroll')
                            ->options(
                                Payroll::with('employee')
                                    ->get()
                                    ->mapWithKeys(fn ($payroll) => [
                                        $payroll->id => $payroll->employee->FNAME . ' ' . $payroll->employee->LNAME
                                            . " [{$payroll->start_date} to {$payroll->end_date}]"
                                    ])
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),


                        Forms\Components\TextInput::make('approval_status')
                            ->default('PENDING')
                            ->disabled()
                            ->dehydrated(),

                        DatePicker::make('approve_date')
                            ->label('Approval Date')
                            ->disabled()
                            ->dehydrated(),

                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table

    {
        $user = Filament::auth()->user();
        return $table
            ->columns([
                TextColumn::make('id')->label('Payslip ID')->sortable(),
               TextColumn::make('employee_full_name')
                    ->label('Employee')
                    ->getStateUsing(fn($record) =>
                        $record->payroll && $record->payroll->employee
                            ? "{$record->payroll->employee->FNAME} {$record->payroll->employee->MNAME} {$record->payroll->employee->LNAME}"
                            : 'N/A'
                    )
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('approver_id')
                    ->label('Reviewed BY')
                    ->getStateUsing(fn ($record) =>
                        $record->approver
                            ? "{$record->approver->FNAME} {$record->approver->MNAME} {$record->approver->LNAME}"
                            : 'N/A'
                    )
                    ->sortable()
                    ->toggleable()
                    ->extraAttributes(['class' => 'text-sm']),

                TextColumn::make('payroll.start_date')->label('Payroll start date')->date()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('payroll.end_date')->label('Payroll end date')->date()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('approval_status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'primary' => 'PENDING',
                        'success' => 'ACCEPTED',
                        'danger' => 'DENIED',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approve_date')->label('Approved At')->date(),
                TextColumn::make('created_at')->label('Created')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
             ->filters([
                    ...(
                in_array($user->ACCESS, ['HR', 'ADMIN'])
                    ? [
                        Tables\Filters\Filter::make('My Requests')
                            ->query(fn (Builder $query) => $query->whereHas('payroll', function ($q) use ($user) {
                                $q->where('employees_id', $user->employee?->id);
                            })),
                    ]
                    : []
            ),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('reviewApprovalStatus')
                    ->label('Review')
                    ->icon('heroicon-o-pencil')
                    ->visible(function ($record) {
                        $user = Filament::auth()->user();
                        return $record->approval_status === 'PENDING' && in_array($user->ACCESS, ['HR', 'ADMIN']);
                    })
                    ->form([
                        Select::make('approval_status')
                            ->label('Approval Status')
                            ->options([
                                'ACCEPTED' => 'Accepted',
                                'DENIED' => 'Denied',
                            ])
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(function (array $data, $record) {
                        $record->approval_status = $data['approval_status'];
                        $record->approve_date = now()->toDateString();
                        $record->approver_id = Filament::auth()->user()?->employee?->id;
                        $record->save();

                        Notification::make()
                            ->title('Payslip reviewed successfully.')
                            ->success()
                            ->send();
                    }),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['payroll.employee', 'payroll.salary']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayslips::route('/'),
            'create' => Pages\CreatePayslip::route('/create'),
            'view' => Pages\ViewPayslip::route('/{record}'),
            'edit' => Pages\EditPayslip::route('/{record}/edit'),
        ];
    }
}
