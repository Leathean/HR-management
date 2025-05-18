<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'job_applications';

    protected $fillable = ['jobpostings_id', 'FNAME', 'MNAME', 'LNAME'];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class, 'jobpostings_id');
    }
}
