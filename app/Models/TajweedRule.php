<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TajweedRule extends Model
{
    protected $fillable = [
        'name_arabic',
        'name_english',
        'name_bangla',
        'description',
        'example',
        'color_code',
        'difficulty_level',
    ];

    protected function casts(): array
    {
        return [
            'difficulty_level' => 'integer',
        ];
    }

    public function studentSkills(): HasMany
    {
        return $this->hasMany(StudentTajweedSkill::class, 'tajweed_rule_id');
    }
}
