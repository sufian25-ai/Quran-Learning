<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'timezone',
        'language',
        'country_code',
        'avatar',
        'is_active',
        'points',
        'last_login_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
            'notification_settings' => 'array',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the teacher profile for this user.
     */
    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class);
    }

    /**
     * Get all enrollments for this user (as a student).
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all active enrollments.
     */
    public function activeEnrollments()
    {
        return $this->enrollments()->where('status', 'active');
    }

    /**
     * Get batches where user is the teacher.
     */
    public function taughtBatches()
    {
        return $this->hasMany(Batch::class, 'teacher_id');
    }

    /**
     * Get classes where user is the teacher.
     */
    public function taughtClasses()
    {
        return $this->hasMany(ClassSession::class, 'teacher_id');
    }

    /**
     * Get all attendance records for this student.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    /**
     * Get all payments made by this user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all reviews written by this user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'student_id');
    }

    /**
     * Get student progress records.
     */
    public function progress()
    {
        return $this->hasMany(StudentProgress::class, 'student_id');
    }

    /**
     * Get all badges earned by this user.
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    /**
     * Get the user's learning streak.
     */
    public function learningStreak()
    {
        return $this->hasOne(LearningStreak::class);
    }

    /**
     * Get all learning streaks for the user.
     */
    public function learningStreaks()
    {
        return $this->hasMany(LearningStreak::class);
    }

    /**
     * Get gamification points for the user.
     */
    public function gamificationPoints()
    {
        return $this->hasMany(GamificationPoint::class);
    }

    /**
     * Get user badges (pivot table records).
     */
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Get certificates earned by this user.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'student_id');
    }

    /**
     * Get support tickets created by this user.
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Get resources uploaded by this user.
     */
    public function uploadedResources()
    {
        return $this->hasMany(Resource::class, 'uploaded_by');
    }

    /**
     * Get Quran surah progress for this user.
     */
    public function surahProgress()
    {
        return $this->hasMany(StudentSurahProgress::class);
    }

    /**
     * Get Tajweed skills for this user.
     */
    public function tajweedSkills()
    {
        return $this->hasMany(StudentTajweedSkill::class);
    }

    /**
     * Get recitation submissions for this user.
     */
    public function recitationSubmissions()
    {
        return $this->hasMany(RecitationSubmission::class);
    }

    /**
     * Get learning sessions for this user.
     */
    public function learningSessions()
    {
        return $this->hasMany(LearningSession::class);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Get avatar URL with fallback.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Return UI Avatar API fallback
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D7C66&color=fff';
    }

    /**
     * Add points to user.
     */
    public function addPoints(int $points): void
    {
        $this->increment('points', $points);
    }

    /**
     * Get upcoming classes for this user (as student).
     */
    public function upcomingClasses()
    {
        $batchIds = $this->enrollments()
            ->where('status', 'active')
            ->whereNotNull('batch_id')
            ->pluck('batch_id');

        return ClassSession::whereIn('batch_id', $batchIds)
            ->where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at')
            ->get();
    }
}
