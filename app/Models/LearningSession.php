<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_date',
        'ayahs_read',
        'ayahs_memorized',
        'ayahs_revised',
        'recitations_submitted',
        'time_spent_minutes',
        'surahs_practiced',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'ayahs_read' => 'integer',
            'ayahs_memorized' => 'integer',
            'ayahs_revised' => 'integer',
            'recitations_submitted' => 'integer',
            'time_spent_minutes' => 'integer',
            'surahs_practiced' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('session_date', $date);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('session_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('session_date', now()->month)
            ->whereYear('session_date', now()->year);
    }
}
