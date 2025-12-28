<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'student_id',
        'teacher_id',
        'enrollment_id',
        'rating',
        'comment',
        'ratings_breakdown',
        'is_verified',
        'is_visible',
        'is_featured',
        'helpful_count',
        'admin_response',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'ratings_breakdown' => 'array',
            'is_verified' => 'boolean',
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // ==================== SCOPES ====================

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // ==================== HELPER METHODS ====================

    public function incrementHelpful(): void
    {
        $this->increment('helpful_count');
    }
}
