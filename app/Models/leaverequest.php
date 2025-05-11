<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class leaverequest extends Model
{
    protected $table = 'leaverequests';

    protected $fillable = [
        'employees_id',
        'LEAVEDATE',
        'RETURNDATE',
        'TOTAL_AMOUNT_LEAVE',
        'REASONS',
        'approver_id',
        'LEAVESTATUS',
        'LEAVETYPE',

    ];

    protected $casts = [
        'LEAVEDATE' => 'date',
        'RETURNDATE' => 'date',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }


}
