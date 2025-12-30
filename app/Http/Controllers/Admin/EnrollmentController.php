<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use App\Models\Batch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnrollmentController extends Controller
{
    /**
     * Display enrollments list
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'course', 'batch']);

        // Search
        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            })->orWhereHas('course', function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => Enrollment::count(),
            'active' => Enrollment::where('status', 'active')->count(),
            'pending' => Enrollment::where('status', 'pending')->count(),
            'completed' => Enrollment::where('status', 'completed')->count(),
        ];

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $enrollments,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    /**
     * Show edit form for enrollment
     */
    public function edit($id)
    {
        $enrollment = Enrollment::with(['user', 'course', 'batch'])->findOrFail($id);

        // Get available teachers (users with 'teacher' role)
        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        })
            ->select('id', 'name', 'email')
            ->get();

        // Get available batches for this course
        $batches = Batch::where('course_id', $enrollment->course_id)
            ->with('teacher:id,name')
            ->get();

        return Inertia::render('Admin/Enrollments/Edit', [
            'enrollment' => [
                'id' => $enrollment->id,
                'user' => $enrollment->user,
                'course' => $enrollment->course,
                'batch' => $enrollment->batch,
                'batch_id' => $enrollment->batch_id,
                'type' => $enrollment->type,
                'status' => $enrollment->status,
                'progress' => $enrollment->progress,
                'amount' => $enrollment->amount,
                'payment_status' => $enrollment->payment_status,
                'created_at' => $enrollment->created_at->format('M d, Y'),
            ],
            'teachers' => $teachers,
            'batches' => $batches
        ]);
    }

    /**
     * Update enrollment (assign teacher/batch)
     */
    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $validated = $request->validate([
            'batch_id' => 'nullable|exists:batches,id',
            'status' => 'required|in:pending,active,completed,cancelled',
            'progress' => 'nullable|integer|min:0|max:100',
            'type' => 'required|in:private,group',
        ]);

        $enrollment->update($validated);

        // If batch is assigned, auto-update status to active
        if ($request->batch_id && $enrollment->status === 'pending') {
            $enrollment->update(['status' => 'active']);
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Quick assign batch to enrollment
     */
    public function assignBatch(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $request->validate([
            'batch_id' => 'required|exists:batches,id',
        ]);

        $enrollment->update([
            'batch_id' => $request->batch_id,
            'status' => 'active' // Auto-activate when batch assigned
        ]);

        return back()->with('success', 'Batch assigned successfully!');
    }

    /**
     * Update enrollment status
     */
    public function updateStatus(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled',
        ]);

        $enrollment->update(['status' => $request->status]);

        // If marked as completed and progress not 100%, set to 100%
        if ($request->status === 'completed' && $enrollment->progress < 100) {
            $enrollment->update(['progress' => 100]);
        }

        return back()->with('success', 'Status updated successfully!');
    }

    /**
     * Delete enrollment
     */
    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return back()->with('success', 'Enrollment deleted successfully!');
    }
}
