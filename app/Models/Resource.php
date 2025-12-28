<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'class_id',
        'uploaded_by',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'external_url',
        'download_count',
        'view_count',
        'is_public',
        'is_downloadable',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'is_downloadable' => 'boolean',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'class_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==================== SCOPES ====================

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    // ==================== HELPER METHODS ====================

    public function getUrlAttribute(): string
    {
        if ($this->external_url) {
            return $this->external_url;
        }

        return asset('storage/' . $this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }
}
