<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\DeductionAttendanceRule;
use App\Models\Salary;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\EmployeeBenefit;

class PayrollService
{
    /**
     * Calculate payroll for an employee within a date range.
     *
     * @param int $employeeId
     * @param int $salaryId
     * @param string|Carbon $startDate
     * @param string|Carbon $endDate
     * @return array
     */
    public function calculatePayroll(int $employeeId, int $salaryId, string $startDate, string $endDate): array
    {
        // Ensure dates are Carbon instances
        $start = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $end = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        // Fetch salary record
        $salary = Salary::where('id', $salaryId)
            ->where('employees_id', $employeeId)
            ->where('STATUS', true)
            ->first();

        if (!$salary) {
            return [
                'gross_salary' => 0.00,
                'total_deductions' => 0.00,
                'net_salary' => 0.00,
                'late_count' => 0,
                'absent_count' => 0,
                'early_out_count' => 0,
                'absent_dates' => [],
                'onleave_dates' => [],
            ];
        }

        $grossSalary = round($salary->BASICSALARY, 2);
        $perDayRate = round($salary->PERDAYRATE, 2);

        $period = CarbonPeriod::create($start, $end);

        $lateCount = 0;
        $absentCount = 0;
        $earlyOutCount = 0;
        $totalDeduction = 0.0;
        $absentDates = [];
        $onleaveDates = [];

        // Fetch deduction rules keyed by ATTENDANCE_TYPE
        $deductionRules = DeductionAttendanceRule::all()->keyBy('ATTENDANCE_TYPE');

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');

            // Get schedule for the date
            $schedule = Schedule::where('employees_id', $employeeId)
                ->where('DATE', $dateStr)
                ->first();

            // If no schedule, skip this day
            if (!$schedule) {
                continue;
            }

            $scheduleType = $schedule->SCHEDULE_TYPE;

            // Get attendance for the date
            $attendance = Attendance::where('employees_id', $employeeId)
                ->where('date', $dateStr)
                ->first();

            if ($scheduleType === 'WORKDAY') {
                if (!$attendance) {
                    $absentCount++;
                    $absentDates[] = $dateStr;

                    // Use deduction rule for ABSENT if available
                    if (isset($deductionRules['ABSENT'])) {
                        $totalDeduction += $this->calculateDeductionAmount($deductionRules['ABSENT'], 1, $grossSalary);
                    } else {
                        // fallback: deduct per day rate
                        $totalDeduction += $perDayRate;
                    }
                } else {
                    // Check late
                    if ($attendance->time_in_status === 'LATE') {
                        $lateCount++;
                        if (isset($deductionRules['LATE'])) {
                            $totalDeduction += $this->calculateDeductionAmount($deductionRules['LATE'], 1, $grossSalary);
                        }
                    }

                    // Check early out
                    if ($attendance->time_out_status === 'EARLY OUT') {
                        $earlyOutCount++;
                        if (isset($deductionRules['EARLY OUT'])) {
                            $totalDeduction += $this->calculateDeductionAmount($deductionRules['EARLY OUT'], 1, $grossSalary);
                        }
                    }
                }
            } elseif ($scheduleType === 'ONLEAVE') {
                $onleaveDates[] = $dateStr;
                $absentCount++;
                // Deduct per day rate on leave
                $totalDeduction += $perDayRate;
            }
            // RESTDAY and LEAVEPAY have no deduction
        }

        // Fetch active employee benefits total
        $activeBenefitsTotal = EmployeeBenefit::where('employees_id', $employeeId)
            ->where('STATUS', true)
            ->sum('AMOUNT');

        $activeBenefitsTotal = round($activeBenefitsTotal, 2);

        $totalDeduction = round($totalDeduction, 2);

        // Net salary after subtracting total deductions and active benefits
        $netSalary = max(0, round($grossSalary - $totalDeduction - $activeBenefitsTotal, 2));

        return [
            'gross_salary' => $grossSalary,
            'total_deductions' => $totalDeduction + $activeBenefitsTotal,
            'net_salary' => $netSalary,
            'late_count' => $lateCount,
            'absent_count' => $absentCount,
            'early_out_count' => $earlyOutCount,
            'absent_dates' => $absentDates,
            'onleave_dates' => $onleaveDates,
        ];
    }

    /**
     * Calculate deduction amount based on rule and count
     *
     * @param DeductionAttendanceRule $rule
     * @param int $count
     * @param float $grossSalary
     * @return float
     */
    protected function calculateDeductionAmount(DeductionAttendanceRule $rule, int $count, float $grossSalary): float
    {
        if ($count === 0) {
            return 0;
        }

        if ($rule->DEDUCTION_METHOD === 'FIXED') {
            return $rule->DEDUCTION_ATTENDANCE_AMOUNT * $count;
        }

        if ($rule->DEDUCTION_METHOD === 'PERCENTAGE') {
            return ($grossSalary * ($rule->DEDUCTION_ATTENDANCE_AMOUNT / 100)) * $count;
        }

        return 0;
    }
}
