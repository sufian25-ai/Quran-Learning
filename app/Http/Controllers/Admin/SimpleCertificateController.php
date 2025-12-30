<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SimpleCertificateController extends Controller
{
    /**
     * Show completed enrollments (students who finished courses)
     */
    public function index()
    {
        // Get completed enrollments without certificates
        $completedEnrollments = Enrollment::with(['user', 'course'])
            ->where('status', 'completed')
            ->whereNotIn('id', function ($query) {
                $query->select('enrollment_id')
                    ->from('certificates')
                    ->whereNotNull('enrollment_id');
            })
            ->latest()
            ->paginate(20);

        // Get all certificates
        $certificates = Certificate::with(['user', 'course'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_certificates' => Certificate::count(),
            'completed_students' => Enrollment::where('status', 'completed')->count(),
            'pending' => $completedEnrollments->total(),
        ];

        return Inertia::render('Admin/Certificates/Simple', [
            'completedEnrollments' => $completedEnrollments,
            'certificates' => $certificates,
            'stats' => $stats
        ]);
    }

    /**
     * Show form to create certificate
     */
    public function create($enrollmentId)
    {
        $enrollment = Enrollment::with(['user', 'course', 'batch.teacher'])
            ->findOrFail($enrollmentId);

        // Check if certificate already exists
        $existing = Certificate::where('enrollment_id', $enrollmentId)->first();
        if ($existing) {
            return back()->with('error', 'Certificate already exists for this enrollment!');
        }

        return Inertia::render('Admin/Certificates/Create', [
            'enrollment' => [
                'id' => $enrollment->id,
                'student_name' => $enrollment->user->name,
                'student_email' => $enrollment->user->email,
                'course_title' => $enrollment->course->title,
                'course_description' => $enrollment->course->description,
                'progress' => $enrollment->progress,
                'instructor' => $enrollment->batch?->teacher?->name ?? 'QuranLearn Instructor',
            ]
        ]);
    }

    /**
     * Store certificate manually
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'student_name' => 'required|string|max:255',
            'course_title' => 'required|string|max:255',
            'course_description' => 'nullable|string',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'grade' => 'nullable|numeric|min:0|max:100',
            'instructor_name' => 'required|string|max:255',
            'issued_by' => 'required|string|max:255',
        ]);

        $enrollment = Enrollment::findOrFail($validated['enrollment_id']);

        // Create certificate
        $certificate = Certificate::create([
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'enrollment_id' => $enrollment->id,
            'student_name' => $validated['student_name'],
            'course_title' => $validated['course_title'],
            'course_description' => $validated['course_description'],
            'completion_percentage' => $validated['completion_percentage'],
            'grade' => $validated['grade'] ?? null,
            'course_started_at' => now()->subDays(30),
            'course_completed_at' => now(),
            'instructor_name' => $validated['instructor_name'],
            'issued_by' => $validated['issued_by'],
            'is_verified' => true,
        ]);

        return redirect()->route('admin.certificates.simple')
            ->with('success', "Certificate created successfully! Number: {$certificate->certificate_number}");
    }

    /**
     * Delete certificate
     */
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->delete();

        return back()->with('success', 'Certificate deleted successfully!');
    }
}
