<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'syllabus',
        'thumbnail',
        'video_preview',
        'level',
        'category',
        'languages',
        'duration_weeks',
        'classes_per_week',
        'class_duration_minutes',
        'price_group',
        'price_private',
        'max_students_per_batch',
        'requirements',
        'learning_outcomes',
        'is_published',
        'is_featured',
        'popularity_score',
        'total_enrollments',
        'average_rating',
        'reviews_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'requirements' => 'array',
            'learning_outcomes' => 'array',
            'price_group' => 'decimal:2',
            'price_private' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get all batches for this course.
     */
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * Get upcoming batches for this course.
     */
    public function upcomingBatches()
    {
        return $this->batches()
            ->whereIn('status', ['upcoming', 'active'])
            ->where('is_accepting_enrollments', true)
            ->orderBy('start_date');
    }

    /**
     * Get all enrollments for this course.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all reviews for this course.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get visible reviews for this course.
     */
    public function visibleReviews()
    {
        return $this->reviews()->where('is_visible', true)->latest();
    }

    /**
     * Get all resources for this course.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * Get public resources for this course.
     */
    public function publicResources()
    {
        return $this->resources()->where('is_public', true);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to only published courses.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to only featured courses.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by level.
     */
    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get thumbnail URL with fallback.
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        return asset('images/course-placeholder.jpg');
    }

    /**
     * Get formatted price with currency.
     */
    public function getFormattedPriceGroupAttribute(): string
    {
        return '$' . number_format($this->price_group, 2);
    }

    /**
     * Get formatted private price with currency.
     */
    public function getFormattedPricePrivateAttribute(): string
    {
        return '$' . number_format($this->price_private, 2);
    }

    /**
     * Update rating based on reviews.
     */
    public function updateRating(): void
    {
        $reviews = $this->reviews()->where('is_visible', true);

        $this->update([
            'average_rating' => $reviews->avg('rating') ?? 0,
            'reviews_count' => $reviews->count(),
        ]);
    }

    /**
     * Increment total enrollments.
     */
    public function incrementEnrollments(): void
    {
        $this->increment('total_enrollments');
        $this->increment('popularity_score', 10);
    }

    /**
     * Get route key name for slug URLs.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
