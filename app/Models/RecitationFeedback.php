<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecitationFeedback extends Model
{
    protected $table = 'recitation_feedback';

    protected $fillable = [
        'submission_id',
        'teacher_id',
        'overall_rating',
        'pronunciation_score',
        'tajweed_score',
        'fluency_score',
        'feedback_text',
        'mistakes',
        'improvements',
        'audio_feedback_path',
    ];

    protected function casts(): array
    {
        return [
            'overall_rating' => 'integer',
            'pronunciation_score' => 'integer',
            'tajweed_score' => 'integer',
            'fluency_score' => 'integer',
            'mistakes' => 'array',
            'improvements' => 'array',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(RecitationSubmission::class, 'submission_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Average score
    public function getAverageScoreAttribute(): int
    {
        return (int) round(($this->pronunciation_score + $this->tajweed_score + $this->fluency_score) / 3);
    }
}
