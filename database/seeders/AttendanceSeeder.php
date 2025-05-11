<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have employees to assign attendance to
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Seeding skipped.');
            return;
        }

        foreach ($employees as $employee) {
            // Simulate 1 days of attendance per employee
            for ($i = 0; $i < 1; $i++) {
                $date = Carbon::now()->subDays($i);
                Attendance::create([
                    'employees_id' => $employee->id,
                    'date' => $date->format('Y-m-d'),
                ]);
            }
        }
    }
}
