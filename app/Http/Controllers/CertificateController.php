<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Display student's certificates
     */
    public function index(Request $request)
    {
        $certificates = $this->certificateService->getUserCertificates($request->user());

        return Inertia::render('Student/Certificates', [
            'certificates' => $certificates
        ]);
    }

    /**
     * Generate certificate for enrollment
     */
    public function generate(Request $request, $enrollmentId)
    {
        $enrollment = $request->user()->enrollments()
            ->where('id', $enrollmentId)
            ->where('status', 'completed')
            ->firstOrFail();

        $certificate = $this->certificateService->generateCertificate($enrollment);

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate generated successfully!');
    }

    /**
     * Download certificate PDF
     */
    public function download($id)
    {
        $certificate = Certificate::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$certificate->pdf_path || !Storage::disk('public')->exists($certificate->pdf_path)) {
            // Regenerate PDF if missing
            $this->certificateService->generatePDF($certificate);
        }

        return Storage::disk('public')->download(
            $certificate->pdf_path,
            "Certificate_{$certificate->certificate_number}.pdf"
        );
    }

    /**
     * View certificate in browser
     */
    public function view($id)
    {
        $certificate = Certificate::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['course', 'user'])
            ->firstOrFail();

        return Inertia::render('Student/CertificateView', [
            'certificate' => [
                'id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'verification_code' => $certificate->verification_code,
                'student_name' => $certificate->student_name,
                'course_title' => $certificate->course_title,
                'course_description' => $certificate->course_description,
                'completion_percentage' => $certificate->completion_percentage,
                'grade' => $certificate->grade,
                'instructor_name' => $certificate->instructor_name,
                'issued_by' => $certificate->issued_by,
                'course_completed_at' => $certificate->course_completed_at->format('F d, Y'),
                'created_at' => $certificate->created_at->format('F d, Y'),
            ]
        ]);
    }

    /**
     * Public certificate verification page
     */
    public function verify($code = null)
    {
        $certificate = null;

        if ($code) {
            $certificate = $this->certificateService->verify($code);
        }

        return Inertia::render('Certificates/Verify', [
            'certificate' => $certificate,
            'searchCode' => $code
        ]);
    }

    /**
     * API: Verify certificate via AJAX
     */
    public function verifyApi(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $certificate = $this->certificateService->verify($request->code);

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate not found or invalid verification code.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'certificate' => [
                'certificate_number' => $certificate->certificate_number,
                'student_name' => $certificate->student_name,
                'course_title' => $certificate->course_title,
                'completion_date' => $certificate->course_completed_at->format('F d, Y'),
                'issue_date' => $certificate->created_at->format('F d, Y'),
                'is_verified' => $certificate->is_verified,
            ]
        ]);
    }
}
