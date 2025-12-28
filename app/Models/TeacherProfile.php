<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'qualifications',
        'certifications',
        'specializations',
        'languages_spoken',
        'hourly_rate',
        'rating',
        'total_reviews',
        'total_students',
        'total_classes',
        'availability',
        'video_intro_url',
        'is_verified',
        'verified_at',
        'is_accepting_students',
    ];

    protected function casts(): array
    {
        return [
            'certifications' => 'array',
            'specializations' => 'array',
            'languages_spoken' => 'array',
            'availability' => 'array',
            'hourly_rate' => 'decimal:2',
            'rating' => 'decimal:2',
            'is_verified' => 'boolean',
            'is_accepting_students' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the user that owns this teacher profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get batches taught by this teacher.
     */
    public function batches()
    {
        return $this->hasMany(Batch::class, 'teacher_id', 'user_id');
    }

    /**
     * Get classes taught by this teacher.
     */
    public function classes()
    {
        return $this->hasMany(ClassSession::class, 'teacher_id', 'user_id');
    }

    /**
     * Get reviews for this teacher.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'teacher_id', 'user_id');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Update rating based on reviews.
     */
    public function updateRating(): void
    {
        $reviews = $this->reviews()->where('is_visible', true);

        $this->update([
            'rating' => $reviews->avg('rating') ?? 0,
            'total_reviews' => $reviews->count(),
        ]);
    }

    /**
     * Increment total students.
     */
    public function incrementStudents(): void
    {
        $this->increment('total_students');
    }

    /**
     * Increment total classes.
     */
    public function incrementClasses(): void
    {
        $this->increment('total_classes');
    }
}
