<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    use HasFactory;

    protected $table = 'student_progress';

    protected $fillable = [
        'enrollment_id',
        'student_id',
        'milestone_type',
        'milestone_id',
        'score',
        'points_earned',
        'metadata',
        'achieved_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'achieved_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // ==================== SCOPES ====================

    public function scopeType($query, $type)
    {
        return $query->where('milestone_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('achieved_at', '>=', now()->subDays($days));
    }
}
