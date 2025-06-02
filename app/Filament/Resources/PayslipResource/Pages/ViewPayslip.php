<?php

namespace App\Filament\Resources\PayslipResource\Pages;

use App\Filament\Resources\PayslipResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section; // For Forms Section, not used here
use Illuminate\Support\Carbon;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection; // Alias to avoid conflict
use Filament\Infolists\Components\TextEntry;

class ViewPayslip extends ViewRecord
{
    protected static string $resource = PayslipResource::class;

    protected function getHeaderActions(): array
    {
        return [
          Actions\EditAction::make(),
            Actions\Action::make('exit')
                ->label('Exit')
                ->url($this->getRedirectUrl())
                ->color('danger'),
        ];
    }

        protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $payslip = $this->record; // Get the current payslip record

        $formatDate = fn($date) => $date ? Carbon::parse($date)->format('Y-m-d') : 'N/A';

        return $infolist->schema([
            InfolistSection::make('Employee Details')
                ->description('Information about the employee.')
                ->schema([
                    TextEntry::make('payroll.employee.FNAME')
                        ->label('Employee Name')
                        ->formatStateUsing(fn ($state, $record) => $record->payroll?->employee
                            ? "{$record->payroll->employee->FNAME} {$record->payroll->employee->MNAME} {$record->payroll->employee->LNAME}"
                            : 'N/A'),

                    TextEntry::make('employees_id')
                        ->label('Employee ID')
                        ->default(fn ($record) => $record->payroll?->employee?->id ?? 'N/A'),

                    TextEntry::make('payroll.employee.ejob.EJOB_NAME')
                        ->label('Job')
                        ->formatStateUsing(fn ($state, $record) => $record->payroll?->employee?->ejob
                            ? "{$record->payroll->employee->ejob->EJOB_NAME} - {$record->payroll->employee->ejob->EJOB_DESCRIPTION}"
                            : 'N/A'),

                    TextEntry::make('payroll.employee.department.DP_NAME')
                        ->label('Department')
                        ->formatStateUsing(fn ($state, $record) => $record->payroll?->employee?->department
                            ? "{$record->payroll->employee->department->DP_NAME} - {$record->payroll->employee->department->DP_DESCRIPTION}"
                            : 'N/A'),

                    TextEntry::make('payroll.employee.PNUMBER')
                        ->label('Phone Number')
                        ->default(fn ($record) => $record->payroll?->employee?->PNUMBER ?? 'N/A'),
                ])
                ->collapsible()
                ->collapsed(false),

            InfolistSection::make('Salary Details')
                ->description('Details about the employee salary.')
                ->schema([
                    TextEntry::make('payroll.salary.BASICSALARY')
                        ->label('Basic Salary')
                        ->formatStateUsing(fn ($state) => is_numeric($state) ? number_format($state, 2) : 'N/A'),

                    TextEntry::make('payroll.salary.PERDAYRATE')
                        ->label('Rate per Day')
                        ->formatStateUsing(fn ($state) => is_numeric($state) ? number_format($state, 2) : 'N/A'),


                    TextEntry::make('payroll.gross_salary')
                        ->label('Gross Salary')
                        ->formatStateUsing(fn ($state) => is_numeric($state) ? number_format($state, 2) : 'N/A'),


                    TextEntry::make('payroll.total_deductions')
                        ->label('total deductions')
                        ->formatStateUsing(fn ($state) => is_numeric($state) ? number_format($state, 2) : 'N/A'),


                    TextEntry::make('payroll.net_salary')
                        ->label('Net Salary')
                        ->formatStateUsing(fn ($state) => is_numeric($state) ? number_format($state, 2) : 'N/A'),

                    TextEntry::make('payroll.salary.STATUS')
                        ->label('Status')
                        ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive'),
                ])
                ->collapsible()
                ->collapsed(false),



            InfolistSection::make('Timestamps')
                ->description('Record creation and update timestamps.')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Date Created')
                        ->formatStateUsing(fn ($state) => optional($state)->format('Y-m-d')),

                    TextEntry::make('updated_at')
                        ->label('Date Updated')
                        ->formatStateUsing(fn ($state) => optional($state)->format('Y-m-d')),
                ])
                ->collapsible()
                ->collapsed(false),



                    InfolistSection::make('Approval')
                        ->description('Payslip approval information.')
                        ->schema([
                            TextEntry::make('approval_status')
                                ->label('Approval Status')
                                ->default($payslip->approval_status ?? 'N/A'),

                            TextEntry::make('approve_date')
                                ->label('Approval Date')
                                ->default($formatDate($payslip->approve_date)),

                            TextEntry::make('approver_name')
                                ->label('Reviewed By')
                                ->default(function () use ($payslip) {
                                    return $payslip->approver
                                        ? "{$payslip->approver->FNAME} {$payslip->approver->MNAME} {$payslip->approver->LNAME}"
                                        : 'N/A';
                                }),

                            TextEntry::make('remarks')
                                ->label('Remarks')
                                ->default($payslip->remarks ?? 'N/A'),
                        ])
                        ->collapsible()
                        ->collapsed(false),
        ]);
    }
}
