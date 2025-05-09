<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'FNAME',
        'MNAME',
        'LNAME',
        'EMAIL',
        'EMPLOYMENT_START',
        'ejob_id',
        'department_id',
        'SALARY',
        'PNUMBER',
        'users_id',
    ];

    public function ejob()
    {
        return $this->belongsTo(Ejob::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }


public function user()
{
    return $this->belongsTo(User::class, 'users_id');
}

public function leaverequest()
{
    return $this->hasMany(LeaveRequest::class, 'employees_id');
}


}

