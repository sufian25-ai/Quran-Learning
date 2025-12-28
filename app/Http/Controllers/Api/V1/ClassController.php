<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Get upcoming classes for authenticated student
     * 
     * @group Classes
     * @authenticated
     */
    public function upcoming(Request $request)
    {
        $user = $request->user();

        // Get batch IDs from active enrollments
        $batchIds = $user->enrollments()
            ->active()
            ->whereNotNull('batch_id')
            ->pluck('batch_id');

        // Get private class enrollment IDs
        $enrollmentIds = $user->enrollments()
            ->active()
            ->whereNull('batch_id')
            ->pluck('id');

        $classes = ClassSession::where(function ($query) use ($batchIds, $enrollmentIds) {
            $query->whereIn('batch_id', $batchIds)
                ->orWhereIn('enrollment_id', $enrollmentIds);
        })
            ->where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->with(['teacher:id,name,avatar', 'batch:id,name,course_id', 'batch.course:id,title'])
            ->orderBy('scheduled_at')
            ->limit($request->get('limit', 10))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $classes->map(function ($class) {
                return [
                    'id' => $class->id,
                    'title' => $class->title,
                    'description' => $class->description,
                    'course_title' => $class->batch?->course?->title ?? 'Private Class',
                    'scheduled_at' => $class->scheduled_at->toISOString(),
                    'duration_minutes' => $class->duration_minutes,
                    'teacher' => $class->teacher ? [
                        'id' => $class->teacher->id,
                        'name' => $class->teacher->name,
                        'avatar_url' => $class->teacher->avatar_url,
                    ] : null,
                    'zoom_join_url' => $class->isStartingSoon() ? $class->zoom_join_url : null,
                    'time_until' => $class->time_until,
                    'is_starting_soon' => $class->isStartingSoon(),
                ];
            }),
        ]);
    }

    /**
     * Get today's classes for authenticated user
     * 
     * @group Classes
     * @authenticated
     */
    public function today(Request $request)
    {
        $user = $request->user();

        // For teachers
        if ($user->isTeacher()) {
            $classes = ClassSession::forTeacher($user->id)
                ->today()
                ->with(['batch:id,name,course_id', 'batch.course:id,title'])
                ->orderBy('scheduled_at')
                ->get();
        } else {
            // For students
            $batchIds = $user->enrollments()->active()->pluck('batch_id');
            $enrollmentIds = $user->enrollments()->active()->whereNull('batch_id')->pluck('id');

            $classes = ClassSession::where(function ($query) use ($batchIds, $enrollmentIds) {
                $query->whereIn('batch_id', $batchIds)
                    ->orWhereIn('enrollment_id', $enrollmentIds);
            })
                ->today()
                ->with(['teacher:id,name,avatar', 'batch:id,name,course_id', 'batch.course:id,title'])
                ->orderBy('scheduled_at')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $classes->map(function ($class) {
                return [
                    'id' => $class->id,
                    'title' => $class->title,
                    'course_title' => $class->batch?->course?->title ?? 'Private Class',
                    'scheduled_at' => $class->scheduled_at->toISOString(),
                    'duration_minutes' => $class->duration_minutes,
                    'status' => $class->status,
                    'zoom_join_url' => $class->zoom_join_url,
                    'teacher' => $class->teacher ? [
                        'name' => $class->teacher->name,
                        'avatar_url' => $class->teacher->avatar_url,
                    ] : null,
                ];
            }),
        ]);
    }

    /**
     * Get single class details
     * 
     * @group Classes
     * @authenticated
     */
    public function show(Request $request, ClassSession $class)
    {
        $user = $request->user();

        // Check access
        $hasAccess = false;
        if ($user->isTeacher() && $class->teacher_id === $user->id) {
            $hasAccess = true;
        } elseif ($class->batch_id) {
            $hasAccess = $user->enrollments()
                ->where('batch_id', $class->batch_id)
                ->active()
                ->exists();
        } elseif ($class->enrollment_id) {
            $hasAccess = $user->enrollments()
                ->where('id', $class->enrollment_id)
                ->active()
                ->exists();
        }

        if (!$hasAccess && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied',
            ], 403);
        }

        $class->load([
            'teacher:id,name,avatar',
            'batch:id,name,course_id',
            'batch.course:id,title',
            'classResources',
        ]);

        // Get attendance if student
        $attendance = null;
        if (!$user->isTeacher()) {
            $attendance = Attendance::where('class_id', $class->id)
                ->where('student_id', $user->id)
                ->first();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $class->id,
                'title' => $class->title,
                'description' => $class->description,
                'objectives' => $class->objectives,
                'course_title' => $class->batch?->course?->title ?? 'Private Class',
                'batch_name' => $class->batch?->name,
                'scheduled_at' => $class->scheduled_at->toISOString(),
                'duration_minutes' => $class->duration_minutes,
                'status' => $class->status,
                'teacher' => $class->teacher ? [
                    'id' => $class->teacher->id,
                    'name' => $class->teacher->name,
                    'avatar_url' => $class->teacher->avatar_url,
                ] : null,
                'zoom' => [
                    'join_url' => $class->zoom_join_url,
                    'password' => $class->zoom_password,
                    'meeting_id' => $class->zoom_meeting_id,
                ],
                'recording_url' => $class->recording_url,
                'resources' => $class->classResources,
                'homework' => $class->homework,
                'teacher_notes' => $class->teacher_notes,
                'attendance' => $attendance ? [
                    'status' => $attendance->status,
                    'joined_at' => $attendance->joined_at?->toISOString(),
                    'duration_minutes' => $attendance->duration_minutes,
                ] : null,
            ],
        ]);
    }

    /**
     * Join class (record attendance)
     * 
     * @group Classes
     * @authenticated
     */
    public function join(Request $request, ClassSession $class)
    {
        $user = $request->user();

        // Verify access
        if ($user->isTeacher()) {
            if ($class->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not the teacher for this class.',
                ], 403);
            }

            // Teacher starting class
            if ($class->status === 'scheduled') {
                $class->start();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'zoom_start_url' => $class->zoom_start_url,
                    'zoom_join_url' => $class->zoom_join_url,
                    'zoom_password' => $class->zoom_password,
                ],
            ]);
        }

        // Student joining
        $hasAccess = false;
        $enrollment = null;

        if ($class->batch_id) {
            $enrollment = $user->enrollments()
                ->where('batch_id', $class->batch_id)
                ->active()
                ->first();
            $hasAccess = $enrollment !== null;
        } elseif ($class->enrollment_id) {
            $enrollment = $user->enrollments()
                ->where('id', $class->enrollment_id)
                ->active()
                ->first();
            $hasAccess = $enrollment !== null;
        }

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this class.',
            ], 403);
        }

        // Record or update attendance
        $attendance = Attendance::updateOrCreate(
            [
                'class_id' => $class->id,
                'student_id' => $user->id,
            ],
            [
                'status' => 'present',
                'joined_at' => now(),
                'marked_at' => now(),
            ]
        );

        // Update enrollment progress
        if ($enrollment && !$attendance->wasRecentlyCreated) {
            $enrollment->incrementClassesAttended();
        }

        // Update user streak
        $user->learningStreak?->recordActivity();

        return response()->json([
            'success' => true,
            'data' => [
                'zoom_join_url' => $class->zoom_join_url,
                'zoom_password' => $class->zoom_password,
                'zoom_meeting_id' => $class->zoom_meeting_id,
            ],
        ]);
    }

    /**
     * Leave class (update attendance duration)
     * 
     * @group Classes
     * @authenticated
     */
    public function leave(Request $request, ClassSession $class)
    {
        $user = $request->user();

        $attendance = Attendance::where('class_id', $class->id)
            ->where('student_id', $user->id)
            ->first();

        if ($attendance) {
            $attendance->update(['left_at' => now()]);
            $attendance->calculateDuration();
        }

        return response()->json([
            'success' => true,
            'message' => 'Class attendance recorded.',
        ]);
    }

    /**
     * Get class history (completed classes)
     * 
     * @group Classes
     * @authenticated
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $batchIds = $user->enrollments()->pluck('batch_id');
        $enrollmentIds = $user->enrollments()->whereNull('batch_id')->pluck('id');

        $classes = ClassSession::where(function ($query) use ($batchIds, $enrollmentIds) {
            $query->whereIn('batch_id', $batchIds)
                ->orWhereIn('enrollment_id', $enrollmentIds);
        })
            ->completed()
            ->with(['teacher:id,name', 'batch:id,name,course_id', 'batch.course:id,title'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $classes->items(),
            'meta' => [
                'current_page' => $classes->currentPage(),
                'last_page' => $classes->lastPage(),
                'per_page' => $classes->perPage(),
                'total' => $classes->total(),
            ],
        ]);
    }
}
