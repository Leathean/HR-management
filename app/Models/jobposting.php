<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class jobposting extends Model
{
    protected $table = 'jobpostings';

    protected $fillable = [
        'ejobs_id',
        'departments_id',
        'POSTED_DATE',
        'QUALIFICATION',
    ];

    protected $casts = [
        'POSTED_DATE' => 'date'
    ];

    public function ejob(): BelongsTo
    {
        return $this->belongsTo(Ejob::class, 'ejobs_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departments_id');
    }
        public function jobApplications()
    {
        return $this->hasMany(JobApplication::class, 'jobpostings_id');
    }
}
