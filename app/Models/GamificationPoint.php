<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GamificationPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'source_type',
        'source_id',
        'description',
        'earned_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'earned_at' => 'datetime',
    ];

    /**
     * Get the user who earned the points.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
