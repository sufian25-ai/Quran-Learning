<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\ClassSession;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $teacherProfile = $user->teacherProfile;

        // Get teacher's batches
        $batches = Batch::where('teacher_id', $user->id)
            ->with(['course:id,title'])
            ->withCount('enrollments')
            ->get();

        // Get today's classes
        $todaysClasses = ClassSession::forTeacher($user->id)
            ->whereDate('scheduled_at', today())
            ->with(['batch:id,name'])
            ->orderBy('scheduled_at')
            ->get();

        // Get upcoming classes (next 7 days)
        $upcomingClasses = ClassSession::forTeacher($user->id)
            ->where('scheduled_at', '>', now())
            ->where('scheduled_at', '<', now()->addDays(7))
            ->where('status', 'scheduled')
            ->with(['batch:id,name'])
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        // Get total students
        $totalStudents = \App\Models\Enrollment::whereIn('batch_id', $batches->pluck('id'))
            ->active()
            ->distinct('user_id')
            ->count();

        return Inertia::render('Teacher/Dashboard', [
            'stats' => [
                'total_students' => $totalStudents,
                'active_batches' => $batches->count(),
                'classes_today' => $todaysClasses->count(),
                'classes_this_week' => $upcomingClasses->count() + $todaysClasses->count(),
                'average_rating' => $teacherProfile?->average_rating ?? 5.0,
                'total_reviews' => $teacherProfile?->total_reviews ?? 0,
            ],
            'todaysClasses' => $todaysClasses->map(fn($class) => [
                'id' => $class->id,
                'title' => $class->title,
                'scheduled_at' => $class->scheduled_at->toISOString(),
                'duration_minutes' => $class->duration_minutes,
                'status' => $class->status,
                'batch' => $class->batch ? ['name' => $class->batch->name] : null,
                'zoom_start_url' => $class->zoom_start_url,
                'enrolled_students' => $class->batch?->enrolled_students ?? 1,
            ]),
            'upcomingClasses' => $upcomingClasses->map(fn($class) => [
                'id' => $class->id,
                'title' => $class->title,
                'scheduled_at' => $class->scheduled_at->toISOString(),
                'batch' => $class->batch ? ['name' => $class->batch->name] : null,
                'enrolled_students' => $class->batch?->enrolled_students ?? 1,
            ]),
            'batches' => $batches->map(fn($batch) => [
                'id' => $batch->id,
                'name' => $batch->name,
                'course' => $batch->course ? ['title' => $batch->course->title] : null,
                'enrolled_students' => $batch->enrollments_count,
            ]),
        ]);
    }
}
