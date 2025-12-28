<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
    ];

    /**
     * Get the user who earned the badge.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the badge earned.
     */
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
