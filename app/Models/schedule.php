<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class schedule extends Model
{
   protected $table = "schedules";

    protected $fillable = [
        'employees_id',
        'NAME',
        'STARTTIME',
        'ENDTIME',
        'SCHEDULE_TYPE',
        'DATE',
    ];


        public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }
}
