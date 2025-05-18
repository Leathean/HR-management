<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ejob extends Model
{
    protected $table = 'ejobs';

    protected $fillable = [
        'EJOB_NAME',
        'EJOB_DESCRIPTION',
    ];
    public function employee()
{
    return $this->hasMany(Employee::class);
}
public function jobPostings()
{
    return $this->hasMany(JobPosting::class, 'ejobs_id');
}
}
