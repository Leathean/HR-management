<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    protected $table = 'payrolls';

    protected $fillable = [
        'employees_id',
        'salary_id',
        'start_date',
        'end_date',
        'gross_salary',
        'total_deductions',
        'net_salary',
    ];

    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salary_id');
    }
}
