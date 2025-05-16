<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'employees_id',
        'date',
        'time_in',
        'time_out',
        'time_in_status',
        'time_out_status',
        'status_day',
    ];

    /**
     * Relationship to Employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    /**
     * Boot method to attach model events.
     */
    protected static function booted()
    {
        // Use saved event to ensure time_in/time_out are set
        static::saved(function ($attendance) {
            $attendance->calculateStatuses();
        });
    }

    /**
     * Calculate and persist attendance statuses based on schedule and times.
     */
    public function calculateStatuses(): void
    {
        $employee = $this->employee;
        if (! $employee) {
            return;
        }

        // Default to absent
        $statusDay     = 'ABSENT';
        $timeInStatus  = null;
        $timeOutStatus = null;

        // Find workday schedule for the date
        $schedule = $employee->schedules()
            ->whereDate('DATE', $this->date)
            ->where('SCHEDULE_TYPE', 'WORKDAY')
            ->first();

        // If there's a schedule and a clock-in, mark present and evaluate
        if ($schedule && $this->time_in) {
            $statusDay = 'PRESENT';

            // Time-in evaluation
            $scheduledStart = Carbon::createFromFormat('H:i:s', $schedule->STARTTIME);
            $clockIn         = Carbon::createFromFormat('H:i:s', $this->time_in);
            $graceTime       = $scheduledStart->copy()->addMinutes(5);
            $timeInStatus    = $clockIn->lte($graceTime) ? 'ON TIME' : 'LATE';

            // Time-out evaluation
            if ($this->time_out) {
                $scheduledEnd   = Carbon::createFromFormat('H:i:s', $schedule->ENDTIME);
                $clockOut       = Carbon::createFromFormat('H:i:s', $this->time_out);
                $timeOutStatus  = $clockOut->gte($scheduledEnd) ? 'ON TIME' : 'EARLY OUT';
            }
        }

        // Update only if changed
        if (
            $this->status_day    !== $statusDay ||
            $this->time_in_status  !== $timeInStatus ||
            $this->time_out_status !== $timeOutStatus
        ) {
            $this->updateQuietly([
                'status_day'      => $statusDay,
                'time_in_status'  => $timeInStatus,
                'time_out_status' => $timeOutStatus,
            ]);
        }
    }
}
