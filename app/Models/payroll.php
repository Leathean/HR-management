<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;
    protected $table ='payrolls';
    protected $fillable = [
        'employees_id',
        'salaries_id',
        'PAYDATE',
        'STATUS',
        'approval_id',
        'approval_date',
        'gross_pay',
        'total_deductions',
        'net_pay',
    ];

    protected $casts = [
        'PAYDATE' => 'date',
        'approval_date' => 'date',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salaries_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approval_id');
    }
}
