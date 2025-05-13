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

public function evaluation()
{
    return $this->hasMany(Evaluation::class, 'employees_id');
}

public function employeebenefit()
{
    return $this->hasMany(EmployeeBenefit::class, 'employees_id');
}
// Direct relationship with Salary
 public function salary()
{
    return $this->hasMany(Salary::class, 'employees_id');  // Direct connection to Salary
}

public function getTotalBenefitsAttribute() // this calculates the total benefits of the said employee
{
    return $this->employeebenefit()->where('STATUS', true)->sum('AMOUNT');
}

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'employees_id');
    }



}

