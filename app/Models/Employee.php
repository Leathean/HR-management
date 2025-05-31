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

    // BelongsTo relationships
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

    // HasMany relationships
    public function leaverequest()
    {
        return $this->hasMany(LeaveRequest::class, 'employees_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'employees_id');
    }

    public function evaluation()
    {
        return $this->hasMany(Evaluation::class, 'employees_id');
    }

    public function employeebenefit()
    {
        return $this->hasMany(EmployeeBenefit::class, 'employees_id');
    }

    public function salary()
    {
        return $this->hasMany(Salary::class, 'employees_id');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'employees_id');
    }

    public function approvedPayrolls()
    {
        return $this->hasMany(Payroll::class, 'approval_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'employees_id');
    }

    // Accessors
    public function getTotalBenefitsAttribute()
    {
        return $this->employeebenefit()->where('STATUS', true)->sum('AMOUNT');
    }

    public function benefits()
    {
        return $this->belongsToMany(Benefit::class, 'employeebenefits', 'employees_id', 'benefits_id')
                    ->withPivot('AMOUNT', 'STATUS')
                    ->withTimestamps();
    }

}


