<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateService
{
    /**
     * Generate certificate for a completed course (Simplified version)
     */
    public function generateCertificate(Enrollment $enrollment): Certificate
    {
        $user = $enrollment->user;
        $course = $enrollment->course;

        // Check if certificate already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Get instructor name
        $instructorName = 'QuranLearn Instructor';
        if ($enrollment->batch && $enrollment->batch->teacher) {
            $instructorName = $enrollment->batch->teacher->name;
        }

        // Create certificate record (WITHOUT PDF generation for now)
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'student_name' => $user->name,
            'course_title' => $course->title,
            'course_description' => $course->description ?? 'Quran Learning Course',
            'completion_percentage' => $enrollment->progress ?? 100,
            'grade' => $enrollment->final_grade ?? null,
            'course_started_at' => now()->subDays(30), // Default 30 days ago
            'course_completed_at' => now(),
            'issued_by' => 'QuranLearn Administration',
            'instructor_name' => $instructorName,
            'is_verified' => true,
        ]);

        return $certificate;
    }

    /**
     * Generate PDF certificate (Optional - call separately)
     */
    public function generatePDF(Certificate $certificate): void
    {
        try {
            $pdf = Pdf::loadView('certificates.template', ['certificate' => $certificate]);

            $fileName = "certificate_{$certificate->certificate_number}.pdf";
            $path = "certificates/{$fileName}";

            // Save PDF to storage
            Storage::disk('public')->put($path, $pdf->output());

            // Update certificate record
            $certificate->update(['pdf_path' => $path]);
        } catch (\Exception $e) {
            \Log::error("PDF generation failed: " . $e->getMessage());
            // Don't throw - certificate still exists without PDF
        }
    }

    /**
     * Generate QR Code for verification (Optional)
     */
    public function generateQRCode(Certificate $certificate): void
    {
        try {
            $verificationUrl = url("/certificates/verify/{$certificate->verification_code}");

            // Generate QR code
            $qrCode = QrCode::format('png')
                ->size(200)
                ->generate($verificationUrl);

            $fileName = "qr_{$certificate->verification_code}.png";
            $path = "certificates/qr/{$fileName}";

            // Save QR code to storage
            Storage::disk('public')->put($path, $qrCode);

            // Update certificate record
            $certificate->update(['qr_code_path' => $path]);
        } catch (\Exception $e) {
            \Log::warning("QR Code generation failed: " . $e->getMessage());
            // Don't throw - certificate still exists without QR
        }
    }

    /**
     * Verify certificate by code
     */
    public function verify(string $code): ?Certificate
    {
        return Certificate::where('verification_code', $code)
            ->where('is_verified', true)
            ->first();
    }

    /**
     * Get user certificates
     */
    public function getUserCertificates(User $user)
    {
        return Certificate::where('user_id', $user->id)
            ->with(['course'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
