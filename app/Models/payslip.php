<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'approver_id',
        'approval_status',
        'approve_date',
        'summary',
        'remarks',
    ];

    protected $casts = [
        'approve_date' => 'date',
        'summary' => 'array',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
}
