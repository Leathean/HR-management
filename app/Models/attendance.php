<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'employees_id',
        'date',
        'time_in',
        'time_out',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    protected static function booted()
    {
        static::saving(function ($attendance) {
            if ($attendance->time_in) {
                $officialStart = '11:00:00';

                $timeIn = date('H:i:s', strtotime($attendance->time_in));

                if ($timeIn > $officialStart) {
                    $attendance->status = 'late';
                } else {
                    $attendance->status = 'on time';
                }
            }
        });
    }
}
