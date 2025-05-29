<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employees_id',
        'BASICSALARY',
        'SALARY_TYPE',
        'STATUS',
    ];

    protected $casts = [
        'STATUS' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'salaries_id');
    }
}
