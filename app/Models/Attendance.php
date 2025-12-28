<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'class_id',
        'student_id',
        'status',
        'joined_at',
        'left_at',
        'duration_minutes',
        'notes',
        'marked_by',
        'marked_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
            'marked_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the class for this attendance record.
     */
    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'class_id');
    }

    /**
     * Get the student for this attendance record.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the user who marked this attendance.
     */
    public function markedByUser()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // ==================== SCOPES ====================

    /**
     * Scope to present students.
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope to absent students.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope to late students.
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if student was present.
     */
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    /**
     * Check if student was absent.
     */
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /**
     * Mark as present.
     */
    public function markPresent($markedBy = null): void
    {
        $this->update([
            'status' => 'present',
            'marked_by' => $markedBy,
            'marked_at' => now(),
        ]);
    }

    /**
     * Mark as absent.
     */
    public function markAbsent($markedBy = null): void
    {
        $this->update([
            'status' => 'absent',
            'marked_by' => $markedBy,
            'marked_at' => now(),
        ]);
    }

    /**
     * Mark as late.
     */
    public function markLate($markedBy = null): void
    {
        $this->update([
            'status' => 'late',
            'marked_by' => $markedBy,
            'marked_at' => now(),
        ]);
    }

    /**
     * Calculate duration from join/leave times.
     */
    public function calculateDuration(): void
    {
        if ($this->joined_at && $this->left_at) {
            $this->update([
                'duration_minutes' => $this->joined_at->diffInMinutes($this->left_at),
            ]);
        }
    }
}
