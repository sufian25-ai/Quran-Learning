<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'batch_id',
        'type',
        'status',
        'amount',
        'currency',
        'billing_cycle',
        'progress_percentage',
        'classes_attended',
        'classes_total',
        'start_date',
        'end_date',
        'next_billing_date',
        'custom_schedule',
        'notes',
        'coupon_code',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'custom_schedule' => 'array',
            'amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'next_billing_date' => 'date',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the user (student) for this enrollment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user - student.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the course for this enrollment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the batch for this enrollment (if group class).
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get all payments for this enrollment.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all classes for this enrollment (for private classes).
     */
    public function classes()
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * Get student progress for this enrollment.
     */
    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    /**
     * Get the certificate for this enrollment (if completed).
     */
    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to active enrollments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to completed enrollments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to group enrollments.
     */
    public function scopeGroup($query)
    {
        return $query->where('type', 'group');
    }

    /**
     * Scope to private enrollments.
     */
    public function scopePrivate($query)
    {
        return $query->where('type', 'private');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if enrollment is group type.
     */
    public function isGroup(): bool
    {
        return $this->type === 'group';
    }

    /**
     * Check if enrollment is private type.
     */
    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    /**
     * Check if enrollment is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Update progress percentage.
     */
    public function updateProgress(): void
    {
        if ($this->classes_total > 0) {
            $percentage = min(100, round(($this->classes_attended / $this->classes_total) * 100));
            $this->update(['progress_percentage' => $percentage]);
        }
    }

    /**
     * Increment classes attended.
     */
    public function incrementClassesAttended(): void
    {
        $this->increment('classes_attended');
        $this->updateProgress();
    }

    /**
     * Mark enrollment as completed.
     */
    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'end_date' => now(),
        ]);
    }

    /**
     * Pause enrollment.
     */
    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    /**
     * Resume paused enrollment.
     */
    public function resume(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Cancel enrollment.
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);

        // Decrement batch enrollment if group
        if ($this->isGroup() && $this->batch) {
            $this->batch->decrementEnrollment();
        }
    }
}
