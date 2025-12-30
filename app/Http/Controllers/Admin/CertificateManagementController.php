<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateManagementController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Display certificate management dashboard
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course', 'enrollment']);

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('certificate_number', 'like', "%{$request->search}%")
                    ->orWhere('student_name', 'like', "%{$request->search}%")
                    ->orWhereHas('course', function ($q) use ($request) {
                        $q->where('title', 'like', "%{$request->search}%");
                    });
            });
        }

        // Filter by course
        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        $certificates = $query->latest()->paginate(20);

        // Get courses for filter dropdown
        $courses = Course::select('id', 'title')->get();

        // Statistics
        $stats = [
            'total_certificates' => Certificate::count(),
            'this_month' => Certificate::whereMonth('created_at', now()->month)->count(),
            'pending_enrollments' => Enrollment::where('status', 'completed')
                ->whereDoesntHave('certificate')
                ->count(),
        ];

        return Inertia::render('Admin/Certificates/Index', [
            'certificates' => $certificates,
            'courses' => $courses,
            'stats' => $stats,
            'filters' => $request->only(['search', 'course_id'])
        ]);
    }

    /**
     * Show eligible enrollments for certificate generation
     */
    public function eligible()
    {
        // Get completed enrollments without certificates
        $eligibleEnrollments = Enrollment::with(['user', 'course'])
            ->where('status', 'completed')
            ->whereDoesntHave('certificate')
            ->latest()
            ->paginate(20);

        // Get all enrollments (for manual certificate issuance)
        $allEnrollments = Enrollment::with(['user', 'course'])
            ->whereDoesntHave('certificate')
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Certificates/Eligible', [
            'eligibleEnrollments' => $eligibleEnrollments,
            'allEnrollments' => $allEnrollments
        ]);
    }

    /**
     * Manually generate certificate for any enrollment
     */
    public function generateManually(Request $request, $enrollmentId)
    {
        $enrollment = Enrollment::with(['user', 'course'])->findOrFail($enrollmentId);

        // Check if certificate already exists
        if ($enrollment->certificate) {
            return back()->with('error', 'Certificate already exists for this enrollment.');
        }

        try {
            $certificate = $this->certificateService->generateCertificate($enrollment);

            return back()->with(
                'success',
                "Certificate generated successfully! Certificate #: {$certificate->certificate_number}"
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    /**
     * Bulk generate certificates for all eligible enrollments
     */
    public function generateBulk()
    {
        $eligibleEnrollments = Enrollment::where('status', 'completed')
            ->whereDoesntHave('certificate')
            ->get();

        $generated = 0;
        foreach ($eligibleEnrollments as $enrollment) {
            try {
                $this->certificateService->generateCertificate($enrollment);
                $generated++;
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error("Failed to generate certificate for enrollment {$enrollment->id}: " . $e->getMessage());
            }
        }

        return back()->with('success', "Generated {$generated} certificates successfully!");
    }

    /**
     * Delete certificate
     */
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);

        // Delete PDF file if exists
        if ($certificate->pdf_path && \Storage::disk('public')->exists($certificate->pdf_path)) {
            \Storage::disk('public')->delete($certificate->pdf_path);
        }

        $certificate->delete();

        return back()->with('success', 'Certificate deleted successfully!');
    }

    /**
     * Show student progress for certificate eligibility
     */
    public function studentProgress()
    {
        $enrollments = Enrollment::with(['user', 'course'])
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Certificates/StudentProgress', [
            'enrollments' => $enrollments
        ]);
    }
}
