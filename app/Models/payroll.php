<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
     protected $fillable = [
        'employees_id', 'salaries_id', 'PAYDATE', 'STATUS'
    ];

    protected $table ='payrolls';


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salaries_id');
    }
    public function employeeBenefit()
    {
        return $this->belongsTo(EmployeeBenefit::class, 'employeebenefits_id');
    }

}
