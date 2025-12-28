<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'enrollment_id',
        'certificate_number',
        'student_name',
        'course_title',
        'course_description',
        'completion_percentage',
        'grade',
        'course_started_at',
        'course_completed_at',
        'pdf_path',
        'template',
        'is_verified',
        'verification_code',
        'qr_code_path',
        'issued_by',
        'instructor_name',
        'instructor_signature',
    ];

    protected $casts = [
        'course_started_at' => 'date',
        'course_completed_at' => 'date',
        'completion_percentage' => 'decimal:2',
        'grade' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    /**
     * Boot function to generate unique codes
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = self::generateCertificateNumber();
            }
            if (empty($certificate->verification_code)) {
                $certificate->verification_code = self::generateVerificationCode();
            }
        });
    }

    /**
     * Generate unique certificate number
     * Format: QLC-YYYY-XXXXX
     */
    public static function generateCertificateNumber(): string
    {
        $year = date('Y');
        $lastCert = self::where('certificate_number', 'like', "QLC-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCert) {
            $lastNumber = (int) substr($lastCert->certificate_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('QLC-%s-%05d', $year, $newNumber);
    }

    /**
     * Generate unique verification code
     * Format: XXXX-XXXX-XXXX
     */
    public static function generateVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get full verification URL
     */
    public function getVerificationUrlAttribute(): string
    {
        return url("/certificates/verify/{$this->verification_code}");
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('certificates.download', $this->id);
    }
}
