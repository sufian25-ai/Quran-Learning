<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'batch_id',
        'enrollment_id',
        'teacher_id',
        'title',
        'description',
        'objectives',
        'scheduled_at',
        'duration_minutes',
        'zoom_meeting_id',
        'zoom_join_url',
        'zoom_start_url',
        'zoom_password',
        'status',
        'started_at',
        'ended_at',
        'recording_url',
        'resources',
        'teacher_notes',
        'homework',
        'attendee_count',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'resources' => 'array',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the batch for this class (for group classes).
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the enrollment for this class (for private classes).
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the teacher for this class.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get attendance records for this class.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    /**
     * Get students who attended this class.
     */
    public function attendees()
    {
        return User::whereIn('id', $this->attendances()->pluck('student_id'));
    }

    /**
     * Get resources attached to this class.
     */
    public function classResources()
    {
        return $this->hasMany(Resource::class, 'class_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope to scheduled classes.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope to live classes.
     */
    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    /**
     * Scope to completed classes.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to upcoming classes.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at');
    }

    /**
     * Scope to today's classes.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    /**
     * Scope for a specific teacher.
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if class is for a group.
     */
    public function isGroupClass(): bool
    {
        return $this->batch_id !== null;
    }

    /**
     * Check if class is private.
     */
    public function isPrivateClass(): bool
    {
        return $this->enrollment_id !== null;
    }

    /**
     * Check if class is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->scheduled_at > now() && $this->status === 'scheduled';
    }

    /**
     * Check if class is starting soon (within 30 minutes).
     */
    public function isStartingSoon(): bool
    {
        return $this->scheduled_at->between(now(), now()->addMinutes(30));
    }

    /**
     * Start the class.
     */
    public function start(): void
    {
        $this->update([
            'status' => 'live',
            'started_at' => now(),
        ]);
    }

    /**
     * End the class.
     */
    public function end(): void
    {
        $this->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);
    }

    /**
     * Cancel the class.
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Get duration in hours and minutes.
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . ' minutes';
    }

    /**
     * Get time until class starts.
     */
    public function getTimeUntilAttribute(): string
    {
        if ($this->scheduled_at <= now()) {
            return 'Started';
        }

        return $this->scheduled_at->diffForHumans();
    }

    /**
     * Set Zoom meeting details.
     */
    public function setZoomMeeting(array $meetingData): void
    {
        $this->update([
            'zoom_meeting_id' => $meetingData['id'] ?? null,
            'zoom_join_url' => $meetingData['join_url'] ?? null,
            'zoom_start_url' => $meetingData['start_url'] ?? null,
            'zoom_password' => $meetingData['password'] ?? null,
        ]);
    }
}
