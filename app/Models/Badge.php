<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'category',
        'criteria',
        'points_value',
    ];

    protected function casts(): array
    {
        return [
            'criteria' => 'array',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    // ==================== SCOPES ====================

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
