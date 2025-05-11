<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class evaluation extends Model
{
       protected $table = 'evaluations';

    protected $fillable = [
        'employees_id',
        'RATINGS',
        'COMMENTS',
    ];

    protected $casts = [
        'RATINGS' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }

    public function getAverageRatingAttribute(): float
{
    if (empty($this->RATINGS)) {
        return 0;
    }

    return round(array_sum($this->RATINGS) / count($this->RATINGS), 2);
}

public function getQuestionsWithRatings()
{
    $ratings = $this->RATINGS ?? [];

    return Questionnaire::whereIn('id', array_keys($ratings))
        ->get()
        ->map(function ($question) use ($ratings) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'RATINGS' => $ratings[$question->id] ?? null,
                'category' => $question->category ?? 'General',
            ];
        });
}

}
