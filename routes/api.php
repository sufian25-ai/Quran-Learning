<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\ClassController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::prefix('v1')->group(function () {

    // ==================== PUBLIC ROUTES ====================

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    });

    // Courses (Public)
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/featured', [CourseController::class, 'featured']);
    Route::get('/courses/categories', [CourseController::class, 'categories']);
    Route::get('/courses/levels', [CourseController::class, 'levels']);
    Route::get('/courses/{slug}', [CourseController::class, 'show']);

    // ==================== PROTECTED ROUTES ====================

    Route::middleware('auth:sanctum')->group(function () {

        // Authentication
        Route::prefix('auth')->group(function () {
            Route::get('/user', [AuthController::class, 'user']);
            Route::put('/user', [AuthController::class, 'updateProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
            Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
        });

        // Enrollments
        Route::prefix('enrollments')->group(function () {
            Route::get('/', [EnrollmentController::class, 'index']);
            Route::get('/active', [EnrollmentController::class, 'active']);
            Route::post('/preview', [EnrollmentController::class, 'preview']);
            Route::post('/', [EnrollmentController::class, 'store']);
            Route::get('/{enrollment}', [EnrollmentController::class, 'show']);
            Route::post('/{enrollment}/cancel', [EnrollmentController::class, 'cancel']);
        });

        // Classes
        Route::prefix('classes')->group(function () {
            Route::get('/upcoming', [ClassController::class, 'upcoming']);
            Route::get('/today', [ClassController::class, 'today']);
            Route::get('/history', [ClassController::class, 'history']);
            Route::get('/{class}', [ClassController::class, 'show']);
            Route::post('/{class}/join', [ClassController::class, 'join']);
            Route::post('/{class}/leave', [ClassController::class, 'leave']);
        });

        // Dashboard Stats
        Route::get('/dashboard/stats', function (Request $request) {
            $user = $request->user();

            $activeEnrollments = $user->enrollments()->active()->count();
            $totalClasses = $user->attendances()->count();
            $streak = $user->learningStreak;

            return response()->json([
                'success' => true,
                'data' => [
                    'active_courses' => $activeEnrollments,
                    'classes_attended' => $totalClasses,
                    'current_streak' => $streak?->current_streak ?? 0,
                    'longest_streak' => $streak?->longest_streak ?? 0,
                    'points' => $user->points,
                    'badges_count' => $user->badges()->count(),
                ],
            ]);
        });

        // ==================== TEACHER ROUTES ====================

        Route::middleware('role:teacher')->prefix('teacher')->group(function () {
            // Teacher batches
            Route::get('/batches', function (Request $request) {
                $batches = $request->user()
                    ->taughtBatches()
                    ->with(['course:id,title', 'enrollments'])
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $batches,
                ]);
            });

            // Teacher classes
            Route::get('/classes/today', function (Request $request) {
                $classes = \App\Models\ClassSession::forTeacher($request->user()->id)
                    ->today()
                    ->with(['batch:id,name,course_id', 'batch.course:id,title'])
                    ->orderBy('scheduled_at')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $classes,
                ]);
            });

            // Mark attendance
            Route::post('/attendance', function (Request $request) {
                $validated = $request->validate([
                    'class_id' => ['required', 'exists:classes,id'],
                    'attendances' => ['required', 'array'],
                    'attendances.*.student_id' => ['required', 'exists:users,id'],
                    'attendances.*.status' => ['required', 'in:present,absent,late,excused'],
                ]);

                $class = \App\Models\ClassSession::findOrFail($validated['class_id']);

                // Verify teacher owns this class
                if ($class->teacher_id !== $request->user()->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized',
                    ], 403);
                }

                foreach ($validated['attendances'] as $record) {
                    \App\Models\Attendance::updateOrCreate(
                        [
                            'class_id' => $class->id,
                            'student_id' => $record['student_id'],
                        ],
                        [
                            'status' => $record['status'],
                            'marked_by' => $request->user()->id,
                            'marked_at' => now(),
                        ]
                    );
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Attendance marked successfully',
                ]);
            });
        });

        // ==================== ADMIN ROUTES ====================

        Route::middleware('role:admin')->prefix('admin')->group(function () {
            // Admin dashboard stats
            Route::get('/stats', function () {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_students' => \App\Models\User::role('student')->count(),
                        'total_teachers' => \App\Models\User::role('teacher')->count(),
                        'total_courses' => \App\Models\Course::count(),
                        'active_enrollments' => \App\Models\Enrollment::active()->count(),
                        'revenue_this_month' => \App\Models\Payment::completed()
                            ->whereMonth('paid_at', now()->month)
                            ->sum('amount'),
                    ],
                ]);
            });

            // Users CRUD
            Route::apiResource('/users', \App\Http\Controllers\Api\V1\Admin\UserController::class);

            // Courses CRUD
            Route::apiResource('/courses', \App\Http\Controllers\Api\V1\Admin\CourseController::class);

            // Batches CRUD
            Route::apiResource('/batches', \App\Http\Controllers\Api\V1\Admin\BatchController::class);
        });
    });
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
});
