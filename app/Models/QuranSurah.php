<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuranSurah extends Model
{
    protected $fillable = [
        'surah_number',
        'name_arabic',
        'name_english',
        'name_transliteration',
        'name_bangla',
        'total_ayahs',
        'revelation_type',
        'revelation_order',
        'juz_start',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'surah_number' => 'integer',
            'total_ayahs' => 'integer',
            'revelation_order' => 'integer',
            'juz_start' => 'integer',
        ];
    }

    public function ayahs(): HasMany
    {
        return $this->hasMany(QuranAyah::class, 'surah_id');
    }

    public function studentProgress(): HasMany
    {
        return $this->hasMany(StudentSurahProgress::class, 'surah_id');
    }

    public function recitations(): HasMany
    {
        return $this->hasMany(RecitationSubmission::class, 'surah_id');
    }

    // Scopes
    public function scopeMeccan($query)
    {
        return $query->where('revelation_type', 'meccan');
    }

    public function scopeMedinan($query)
    {
        return $query->where('revelation_type', 'medinan');
    }

    // Helpers
    public function getFullNameAttribute(): string
    {
        return $this->surah_number . '. ' . $this->name_english . ' (' . $this->name_arabic . ')';
    }
}
