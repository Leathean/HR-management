<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class employeebenefit extends Model
{
protected $fillable = [
        'employees_id',
        'benefits_id',
        'STATUS',
        'AMOUNT',
    ];
    protected $table = 'employeebenefits';

public function employee()
{
    return $this->belongsTo(Employee::class, 'employees_id');
}

public function benefit()
{
    return $this->belongsTo(Benefit::class, 'benefits_id');
}
    public function salary()
    {
        return $this->hasMany(Salary::class, 'employeebenefits_id');  // Corrected the foreign key
    }
}
