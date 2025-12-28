<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTajweedSkill extends Model
{
    protected $fillable = [
        'user_id',
        'tajweed_rule_id',
        'skill_level',
        'practice_count',
        'correct_count',
        'mistake_count',
        'last_practiced_at',
    ];

    protected function casts(): array
    {
        return [
            'skill_level' => 'integer',
            'practice_count' => 'integer',
            'correct_count' => 'integer',
            'mistake_count' => 'integer',
            'last_practiced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(TajweedRule::class, 'tajweed_rule_id');
    }

    // Accuracy percentage
    public function getAccuracyAttribute(): int
    {
        if ($this->practice_count === 0)
            return 0;
        return (int) round(($this->correct_count / $this->practice_count) * 100);
    }
}
