<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AttendanceDeductionSeeder extends Seeder
{
  public function run()
    {
        // Clear existing records to prevent duplicates if running multiple times
        DB::table('deduction_attendance_rules')->truncate();

        // Insert the specified attendance deduction rules
        DB::table('deduction_attendance_rules')->insert([
            [
                'ATTENDANCE_TYPE' => 'LATE',
                'DEDUCTION_METHOD' => 'FIXED',
                'DEDUCTION_ATTENDANCE_AMOUNT' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ATTENDANCE_TYPE' => 'ABSENT',
                'DEDUCTION_METHOD' => 'PERCENTAGE',
                'DEDUCTION_ATTENDANCE_AMOUNT' => 1.00, // Represents 1%
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ATTENDANCE_TYPE' => 'EARLY OUT',
                'DEDUCTION_METHOD' => 'FIXED',
                'DEDUCTION_ATTENDANCE_AMOUNT' => 20.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
