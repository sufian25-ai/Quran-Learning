<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use App\Services\ZoomService;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /**
     * Display a listing of batches.
     */
    public function index(Request $request)
    {
        $query = Batch::with(['course:id,title', 'teacher:id,name'])
            ->withCount('enrollments');

        // Filter by course
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by teacher
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $batches = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $batches->items(),
            'meta' => [
                'current_page' => $batches->currentPage(),
                'last_page' => $batches->lastPage(),
                'per_page' => $batches->perPage(),
                'total' => $batches->total(),
            ],
        ]);
    }

    /**
     * Store a newly created batch.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_students' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date', 'after:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'schedule' => ['required', 'array'],
            'schedule.*.day' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'schedule.*.time' => ['required', 'date_format:H:i'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:draft,upcoming,active'],
        ]);

        // Verify teacher has teacher role
        $teacher = User::findOrFail($validated['teacher_id']);
        if (!$teacher->hasRole('teacher')) {
            return response()->json([
                'success' => false,
                'message' => 'Selected user is not a teacher.',
            ], 400);
        }

        $batch = Batch::create($validated);

        // Generate classes for the batch
        $this->generateClasses($batch);

        return response()->json([
            'success' => true,
            'message' => 'Batch created successfully',
            'data' => $batch->load(['course', 'teacher']),
        ], 201);
    }

    /**
     * Display the specified batch.
     */
    public function show(Batch $batch)
    {
        $batch->load([
            'course',
            'teacher.teacherProfile',
            'enrollments.user',
            'classes',
        ]);

        return response()->json([
            'success' => true,
            'data' => $batch,
        ]);
    }

    /**
     * Update the specified batch.
     */
    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'teacher_id' => ['sometimes', 'exists:users,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_students' => ['sometimes', 'integer', 'min:' . $batch->enrolled_students],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'schedule' => ['sometimes', 'array'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:draft,upcoming,active,completed,cancelled'],
            'is_accepting_enrollments' => ['boolean'],
        ]);

        $batch->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Batch updated successfully',
            'data' => $batch->fresh()->load(['course', 'teacher']),
        ]);
    }

    /**
     * Remove the specified batch.
     */
    public function destroy(Batch $batch)
    {
        // Check if batch has enrollments
        if ($batch->enrolled_students > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete batch with enrolled students.',
            ], 400);
        }

        $batch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Batch deleted successfully',
        ]);
    }

    /**
     * Generate class schedules for a batch.
     */
    protected function generateClasses(Batch $batch): void
    {
        $course = $batch->course;
        $startDate = $batch->start_date;
        $totalWeeks = $course->duration_weeks;
        $schedule = $batch->schedule;

        $currentDate = $startDate->copy();
        $endDate = $startDate->copy()->addWeeks($totalWeeks);
        $classNumber = 1;

        while ($currentDate <= $endDate) {
            foreach ($schedule as $slot) {
                $dayOfWeek = strtolower($currentDate->format('l'));

                if ($dayOfWeek === $slot['day']) {
                    $classDateTime = $currentDate->copy()->setTimeFromTimeString($slot['time']);

                    // Only create future classes
                    if ($classDateTime > now()) {
                        \App\Models\ClassSession::create([
                            'batch_id' => $batch->id,
                            'teacher_id' => $batch->teacher_id,
                            'title' => "Class {$classNumber}: {$course->title}",
                            'scheduled_at' => $classDateTime,
                            'duration_minutes' => $course->class_duration_minutes,
                            'status' => 'scheduled',
                        ]);

                        $classNumber++;
                    }
                }
            }

            $currentDate->addDay();
        }

        // Update batch end date
        $batch->update(['end_date' => $endDate]);
    }

    /**
     * Create Zoom meetings for all scheduled classes.
     */
    public function createZoomMeetings(Batch $batch)
    {
        $zoomService = app(ZoomService::class);
        $classes = $batch->classes()->scheduled()->get();
        $created = 0;
        $failed = 0;

        foreach ($classes as $class) {
            if (!$class->zoom_meeting_id) {
                $result = $zoomService->createMeeting($class);

                if ($result['success']) {
                    $created++;
                } else {
                    $failed++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Zoom meetings created: {$created}, failed: {$failed}",
        ]);
    }
}
