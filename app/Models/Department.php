<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'DP_NAME',
        'DP_DESCRIPTION'
    ];

    protected $table = 'departments';
public function employee()
{
    return $this->hasMany(Employee::class, 'department_id');
}
public function jobPostings()
{
    return $this->hasMany(JobPosting::class, 'departments_id');
}
}
