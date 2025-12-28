<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSurahProgress extends Model
{
    protected $table = 'student_surah_progress';

    protected $fillable = [
        'user_id',
        'surah_id',
        'status',
        'last_ayah_read',
        'ayahs_memorized',
        'read_count',
        'listen_count',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'last_ayah_read' => 'integer',
            'ayahs_memorized' => 'integer',
            'read_count' => 'integer',
            'listen_count' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
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

    // Progress percentage
    public function getProgressPercentageAttribute(): int
    {
        if (!$this->surah)
            return 0;
        return (int) round(($this->last_ayah_read / $this->surah->total_ayahs) * 100);
    }

    // Memorization percentage
    public function getMemorizationPercentageAttribute(): int
    {
        if (!$this->surah)
            return 0;
        return (int) round(($this->ayahs_memorized / $this->surah->total_ayahs) * 100);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeMemorized($query)
    {
        return $query->where('status', 'memorized');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
