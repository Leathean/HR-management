<?php

namespace App\Filament\Resources\SalaryResource\Pages;

use App\Filament\Resources\SalaryResource;
use App\Models\Salary;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Models\Employee;
class CreateSalary extends CreateRecord
{
    protected static string $resource = SalaryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $firstSalary = null;

        if (empty($data['employees_id']) || !is_array($data['employees_id'])) {
            abort(422, 'No employees selected.');
        }

        foreach ($data['employees_id'] as $index => $employeeId) {
            $salary = Salary::create([
                'employees_id' => $employeeId,
                'BASICSALARY' => $data['BASICSALARY'],
                'SALARY_TYPE' => $data['SALARY_TYPE'],
                'STATUS' => $data['STATUS'] ?? true,
            ]);

            if ($index === 0) {
                $firstSalary = $salary;
            }
        }

        if (!$firstSalary) {
            abort(500, 'Failed to create any Salary records.');
        }

        return $firstSalary;
    }

            protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount(): void
    {
        parent::mount();

        $hasAvailableEmployees = Employee::whereDoesntHave('salary')->exists();

        if (! $hasAvailableEmployees) {
            Notification::make()
                ->title('No available employees')
                ->body('All employees already have a salary assigned.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(SalaryResource::getUrl('index'));
        }
    }
}
