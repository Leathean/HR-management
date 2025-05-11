<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Questionnaire extends Model
{
    protected $table = 'questionnaires';

    protected $fillable = [
        'QUESTION',
        'CATEGORY',
        'DESCRIPTION',
        'IS_ACTIVE',
        'ORDER',
    ];

    protected $casts = [
        'IS_ACTIVE' => 'boolean',
    ];

    protected $attributes = [
        'IS_ACTIVE' => true,
    ];

    /**
     * Scope for active questions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('IS_ACTIVE', true);
    }

    /**
     * Scope for questions in a specific category.
     */
    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->where('CATEGORY', $category);
    }

    /**
     * Scope for ordered questions.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('ORDER')->orderBy('id');
    }

    /**
     * Query evaluations where this question's rating is null in a JSON field.
     */
    public function evaluationsWithNullRating()
    {
        return \App\Models\Evaluation::whereJsonContains('ratings', [$this->id => null]);
    }
}

