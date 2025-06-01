<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\PayrollService;
use App\Models\Payroll;
use Filament\Notifications\Notification;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        $employeeId = $data['employees_id'];
        $salaryId = $data['salary_id'];
        $startDate = $data['start_date'];
        $endDate = $data['end_date'];

        // Use the service
        $payrollService = app(PayrollService::class);
        $payrollData = $payrollService->calculatePayroll($employeeId, $salaryId, $startDate, $endDate);

        if ($payrollData['net_salary'] <= 0) {
            Notification::make()
                ->title('Invalid Net Salary')
                ->body('Net salary is zero or negative. Payroll creation has been cancelled. Please review deductions or employee benefits.')
                ->danger()
                ->send();

            $this->halt(); // Gracefully stop without throwing
        }

        // Fill form with calculated data
        $this->form->fill([
            ...$data,
            'gross_salary' => $payrollData['gross_salary'],
            'total_deductions' => $payrollData['total_deductions'],
            'net_salary' => $payrollData['net_salary'],
        ]);
    }

    protected function handleRecordCreation(array $data): Payroll
    {
        return Payroll::create($data);
    }
}
