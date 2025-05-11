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
public function attendance()
{
    return $this->hasMany(attendance::class, 'employees_id');
}

public function employeeBenefits()
{
    return $this->hasMany(EmployeeBenefit::class, 'employees_id');
}

public function evaluation()
{
    return $this->hasMany(Evaluation::class, 'employees_id');
}


}

