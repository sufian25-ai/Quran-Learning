<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RecitationSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'surah_id',
        'ayah_from',
        'ayah_to',
        'audio_path',
        'duration_seconds',
        'status',
        'reviewed_by',
        'reviewed_at',
        'student_notes',
    ];

    protected function casts(): array
    {
        return [
            'ayah_from' => 'integer',
            'ayah_to' => 'integer',
            'duration_seconds' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(QuranSurah::class, 'surah_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(RecitationFeedback::class, 'submission_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    // Helpers
    public function getAyahRangeAttribute(): string
    {
        if ($this->ayah_from === $this->ayah_to) {
            return $this->surah?->surah_number . ':' . $this->ayah_from;
        }
        return $this->surah?->surah_number . ':' . $this->ayah_from . '-' . $this->ayah_to;
    }

    public function getAudioUrlAttribute(): string
    {
        return asset('storage/' . $this->audio_path);
    }
}
