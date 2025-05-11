<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class benefit extends Model
{
        protected $fillable = [
        'NAME',
        'DESCRIPTION'
    ];

    protected $table = 'benefits';
public function employeeBenefits()
{
    return $this->hasMany(EmployeeBenefit::class, 'benefits_id');
}
}
