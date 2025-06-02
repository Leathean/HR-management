<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\PayrollService;
use App\Models\Payroll;
use Filament\Notifications\Notification;
use Filament\Exceptions\Halt;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $employeeId = $data['employees_id'];
        $salaryId = $data['salary_id'];
        $startDate = $data['start_date'];
        $endDate = $data['end_date'];

        // Check duplicate payroll
        $existing = Payroll::where('employees_id', $employeeId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($query) use ($startDate, $endDate) {
                          $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                      });
            })->exists();

        if ($existing) {
            Notification::make()
                ->title('Duplicate Payroll Detected')
                ->body('A payroll for this employee already exists within the selected date range.')
                ->danger()
                ->send();

         throw $this->halt();
        }

        $payrollService = app(PayrollService::class);
        $payrollData = $payrollService->calculatePayroll($employeeId, $salaryId, $startDate, $endDate);

        if ($payrollData['net_salary'] <= 0) {
            Notification::make()
                ->title('Invalid Net Salary')
                ->body('Net salary is zero or negative. Payroll creation has been cancelled.')
                ->danger()
                ->send();

            throw $this->halt();
        }

        $data['gross_salary'] = $payrollData['gross_salary'];
        $data['total_deductions'] = $payrollData['total_deductions'];
        $data['net_salary'] = $payrollData['net_salary'];

        return $data;
    }

    protected function handleRecordCreation(array $data): Payroll
    {
        return Payroll::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
