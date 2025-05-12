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
        return $this->belongsTo(EmployeeBenefit::class, 'employeebenefit_id');
    }
}
