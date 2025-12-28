<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuranAyah extends Model
{
    protected $fillable = [
        'surah_id',
        'ayah_number',
        'ayah_key',
        'text_arabic',
        'text_uthmani',
        'translation_english',
        'translation_bangla',
        'transliteration',
        'audio_url',
        'juz_number',
        'hizb_number',
        'page_number',
    ];

    protected function casts(): array
    {
        return [
            'ayah_number' => 'integer',
            'ayah_key' => 'integer',
            'juz_number' => 'integer',
            'hizb_number' => 'integer',
            'page_number' => 'integer',
        ];
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(QuranSurah::class, 'surah_id');
    }

    // Get reference like "2:255"
    public function getReferenceAttribute(): string
    {
        return $this->surah?->surah_number . ':' . $this->ayah_number;
    }
}
