<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'name',
        'description',
        'max_students',
        'enrolled_students',
        'start_date',
        'end_date',
        'schedule',
        'status',
        'price_override',
        'is_accepting_enrollments',
    ];

    protected function casts(): array
    {
        return [
            'schedule' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'price_override' => 'decimal:2',
            'is_accepting_enrollments' => 'boolean',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the course for this batch.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the teacher for this batch.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get all enrollments for this batch.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get active enrollments.
     */
    public function activeEnrollments()
    {
        return $this->enrollments()->where('status', 'active');
    }

    /**
     * Get all classes for this batch.
     */
    public function classes()
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * Get upcoming classes.
     */
    public function upcomingClasses()
    {
        return $this->classes()
            ->where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at');
    }

    /**
     * Get students enrolled in this batch.
     */
    public function students()
    {
        return User::whereIn('id', $this->enrollments()->pluck('user_id'));
    }

    // ==================== SCOPES ====================

    /**
     * Scope to upcoming batches.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    /**
     * Scope to active batches.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to batches accepting enrollments.
     */
    public function scopeAcceptingEnrollments($query)
    {
        return $query->where('is_accepting_enrollments', true)
            ->whereColumn('enrolled_students', '<', 'max_students');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if batch has available slots.
     */
    public function hasAvailableSlots(): bool
    {
        return $this->enrolled_students < $this->max_students;
    }

    /**
     * Get available slots count.
     */
    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->max_students - $this->enrolled_students);
    }

    /**
     * Get the effective price (override or course price).
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->price_override ?? $this->course->price_group;
    }

    /**
     * Get formatted schedule for display.
     */
    public function getFormattedScheduleAttribute(): string
    {
        if (!$this->schedule) {
            return 'Schedule TBD';
        }

        return collect($this->schedule)
            ->map(fn($slot) => ucfirst($slot['day']) . ' at ' . $slot['time'])
            ->join(', ');
    }

    /**
     * Increment enrolled students count.
     */
    public function incrementEnrollment(): void
    {
        $this->increment('enrolled_students');

        // Close enrollments if full
        if ($this->enrolled_students >= $this->max_students) {
            $this->update(['is_accepting_enrollments' => false]);
        }
    }

    /**
     * Decrement enrolled students count.
     */
    public function decrementEnrollment(): void
    {
        $this->decrement('enrolled_students');

        // Reopen enrollments if was full
        if ($this->enrolled_students < $this->max_students && $this->status !== 'completed') {
            $this->update(['is_accepting_enrollments' => true]);
        }
    }
}
