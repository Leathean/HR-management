<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class salary extends Model
{
    protected $table = 'salaries';

        protected $fillable = [
        'employees_id',
        'BASICSALARY',
        'employeebenefit_id',
        'NETSALARY',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    public function employeeBenefit()
    {
        return $this->belongsTo(EmployeeBenefit::class, 'employeebenefits_id');
    }


//testing that a employee has a many payroll will change later to 1 payroll per 1 salary per 1 month computation
    public function payroll()
    {
        return $this->hasMany(Payroll::class, 'salaries_id');
    }
}
