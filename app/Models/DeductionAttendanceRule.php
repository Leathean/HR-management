<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeductionAttendanceRule extends Model
{
  protected $fillable = [
        'ATTENDANCE_TYPE',
        'DEDUCTION_METHOD',
        'DEDUCTION_ATTENDANCE_AMOUNT',
    ];

    // Optional: constants for type safety and easy dropdowns
    const ATTENDANCE_TYPES = ['LATE', 'ABSENT', 'EARLY OUT'];
    const DEDUCTION_METHODS = ['FIXED', 'PERCENTAGE'];

    // Casts
    protected $casts = [
        'DEDUCTION_ATTENDANCE_AMOUNT' => 'decimal:2',
    ];

}
