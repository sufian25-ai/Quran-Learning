<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningStreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_streak',
        'longest_streak',
        'last_activity_date',
    ];

    protected function casts(): array
    {
        return [
            'last_activity_date' => 'date',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Record activity and update streak.
     */
    public function recordActivity(): void
    {
        $today = now()->toDateString();
        $lastActivity = $this->last_activity_date?->toDateString();

        if ($lastActivity === $today) {
            // Already recorded today
            return;
        }

        $yesterday = now()->subDay()->toDateString();

        if ($lastActivity === $yesterday) {
            // Continue streak
            $this->current_streak++;
        } else {
            // Reset streak
            $this->current_streak = 1;
        }

        // Update longest streak
        if ($this->current_streak > $this->longest_streak) {
            $this->longest_streak = $this->current_streak;
        }

        $this->last_activity_date = $today;
        $this->save();
    }

    /**
     * Check if streak is active today.
     */
    public function isActiveToday(): bool
    {
        return $this->last_activity_date?->toDateString() === now()->toDateString();
    }
}
