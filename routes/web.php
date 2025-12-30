<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Models\Course;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// SEO Routes
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index']);

// Landing Page
Route::get('/', function () {
    $featuredCourses = Course::published()
        ->featured()
        ->orderBy('popularity_score', 'desc')
        ->limit(6)
        ->get();

    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'featuredCourses' => $featuredCourses,
        'stats' => [
            'students' => 5000,
            'teachers' => 50,
            'courses' => Course::published()->count() ?: 20,
            'countries' => 45,
        ],
    ]);
})->name('home');

// Pricing Page
Route::get('/pricing', function () {
    return Inertia::render('Pricing');
})->name('pricing');

// About Page
Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

// Contact Page
Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

// ==========================================
// PUBLIC QURAN READING (No Auth Required - SEO Friendly)
// ==========================================

// Public Quran Reader - Surah based
Route::get('/read-quran', function () {
    $surahs = \App\Models\QuranSurah::orderBy('surah_number')->get();

    return Inertia::render('Public/QuranReader', [
        'surahs' => $surahs->map(fn($s) => [
            'id' => $s->id,
            'surah_number' => $s->surah_number,
            'name_arabic' => $s->name_arabic,
            'name_english' => $s->name_english,
            'name_bangla' => $s->name_bangla,
            'total_ayahs' => $s->total_ayahs,
            'revelation_type' => $s->revelation_type,
        ]),
    ]);
})->name('public.quran');

// Public Hifz Reader - Para/Page based
Route::get('/hifz-quran', function () {
    return Inertia::render('Public/HifzReader');
})->name('public.hifz');

// Teachers Page
Route::get('/teachers', function () {
    $teachers = \App\Models\User::role('teacher')
        ->with('teacherProfile')
        ->whereHas('teacherProfile', fn($q) => $q->where('is_verified', true))
        ->limit(12)
        ->get();

    // Gradient colors for teacher cards
    $gradients = [
        'from-emerald-500 to-teal-600',
        'from-purple-500 to-pink-600',
        'from-blue-500 to-indigo-600',
        'from-amber-500 to-orange-600',
        'from-rose-500 to-pink-600',
        'from-cyan-500 to-blue-600',
    ];

    return Inertia::render('Teachers', [
        'teachers' => $teachers->map(fn($t, $index) => [
            'id' => $t->id,
            'name' => $t->name,
            'avatar' => $t->avatar_url,
            'specializations' => $t->teacherProfile?->specializations ?? [],
            'bio' => $t->teacherProfile?->bio ?? 'Experienced Quran teacher dedicated to helping students excel in their learning journey.',
            'rating' => (float) ($t->teacherProfile?->rating ?? 5.0),
            'reviews_count' => $t->teacherProfile?->total_reviews ?? 0,
            'students_taught' => $t->teacherProfile?->total_students ?? 0,
            'languages' => $t->teacherProfile?->languages_spoken ?? ['English'],
            'is_available' => $t->teacherProfile?->is_accepting_students ?? true,
            'gradient' => $gradients[$index % count($gradients)],
        ]),
    ]);
})->name('teachers');

// ==========================================
// PUBLIC CHAT API (No Auth Required)
// ==========================================
Route::prefix('api/chat')->group(function () {
    Route::post('/start', [\App\Http\Controllers\ChatController::class, 'startConversation']);
    Route::post('/{id}/messages', [\App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::get('/{id}/messages', [\App\Http\Controllers\ChatController::class, 'getMessages']);
});


// Course Catalog
Route::get('/courses', function () {
    $query = Course::published();

    // Apply filters
    if (request()->has('category')) {
        $query->where('category', request('category'));
    }
    if (request()->has('level')) {
        $query->where('level', request('level'));
    }
    if (request()->has('search')) {
        $search = request('search');
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%");
        });
    }

    // Apply sorting
    $sort = request('sort', 'popularity');
    switch ($sort) {
        case 'newest':
            $query->latest();
            break;
        case 'price_low':
            $query->orderBy('price_group', 'asc');
            break;
        case 'price_high':
            $query->orderBy('price_group', 'desc');
            break;
        case 'rating':
            $query->orderBy('average_rating', 'desc');
            break;
        default:
            $query->orderBy('popularity_score', 'desc');
    }

    $courses = $query->paginate(12);

    return Inertia::render('Courses/Index', [
        'courses' => [
            'data' => $courses->map(fn($course) => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'short_description' => $course->short_description,
                'thumbnail' => $course->thumbnail_url,
                'level' => $course->level,
                'category' => $course->category,
                'duration_weeks' => $course->duration_weeks,
                'classes_per_week' => $course->classes_per_week,
                'is_featured' => $course->is_featured,
                'pricing' => [
                    'group' => $course->price_group,
                    'private' => $course->price_private,
                    'formatted_group' => $course->formatted_price_group,
                ],
                'rating' => [
                    'average' => $course->average_rating,
                    'count' => $course->reviews_count,
                ],
            ]),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ],
        'filters' => request()->only(['search', 'category', 'level', 'sort']),
    ]);
})->name('courses.index');

// Course Detail
Route::get('/courses/{slug}', function ($slug) {
    $course = Course::where('slug', $slug)
        ->published()
        ->with([
            'batches' => function ($q) {
                $q->whereIn('status', ['upcoming', 'active'])
                    ->where('is_accepting_enrollments', true)
                    ->with('teacher:id,name,avatar')
                    ->orderBy('start_date');
            },
            'visibleReviews' => function ($q) {
                $q->with('student:id,name,avatar')
                    ->limit(10);
            },
        ])
        ->firstOrFail();

    return Inertia::render('Courses/Show', [
        'course' => [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'short_description' => $course->short_description,
            'description' => $course->description,
            'syllabus' => $course->syllabus,
            'level' => $course->level,
            'category' => $course->category,
            'duration_weeks' => $course->duration_weeks,
            'classes_per_week' => $course->classes_per_week,
            'class_duration_minutes' => $course->class_duration_minutes,
            'is_featured' => $course->is_featured,
            'total_enrollments' => $course->total_enrollments,
            'pricing' => [
                'group' => $course->price_group,
                'private' => $course->price_private,
            ],
            'rating' => [
                'average' => $course->average_rating,
                'count' => $course->reviews_count,
            ],
            'learning_outcomes' => $course->learning_outcomes,
            'requirements' => $course->requirements,
            'batches' => $course->batches->map(fn($batch) => [
                'id' => $batch->id,
                'name' => $batch->name,
                'start_date' => $batch->start_date->toDateString(),
                'formatted_schedule' => $batch->formatted_schedule,
                'available_slots' => $batch->available_slots,
                'teacher' => $batch->teacher ? [
                    'id' => $batch->teacher->id,
                    'name' => $batch->teacher->name,
                ] : null,
            ]),
            'reviews' => $course->visibleReviews->map(fn($review) => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'student' => $review->student ? [
                    'name' => $review->student->name,
                ] : null,
            ]),
        ],
    ]);
})->name('courses.show');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Dynamic Dashboard Redirect - Routes users to their role-specific dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        }

        // Default to student dashboard
        return redirect()->route('student.dashboard');
    })->name('dashboard');

    // Settings Page (for all authenticated users)
    Route::get('/settings', function () {
        $user = auth()->user();

        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'timezone' => $user->timezone ?? 'UTC',
            'language' => $user->language ?? 'en',
            'country_code' => $user->country_code ?? '',
        ];

        // Render appropriate settings page based on role
        if ($user->hasRole('teacher')) {
            return Inertia::render('Teacher/Settings', ['user' => $userData]);
        }

        return Inertia::render('Student/Settings', ['user' => $userData]);
    })->name('settings');

    // Support Page (for all authenticated users)
    Route::get('/support', function () {
        $user = auth()->user();

        $tickets = \App\Models\SupportTicket::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $ticketsData = $tickets->map(fn($t) => [
            'id' => $t->id,
            'subject' => $t->subject,
            'message' => $t->message,
            'status' => $t->status,
            'priority' => $t->priority,
            'created_at' => $t->created_at->toISOString(),
        ]);

        // Render appropriate support page based on role
        if ($user->hasRole('teacher')) {
            return Inertia::render('Teacher/Support', ['tickets' => $ticketsData]);
        }

        return Inertia::render('Student/Support', ['tickets' => $ticketsData]);
    })->name('support');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    // Student Dashboard
    Route::get('/student/dashboard', function () {
        $user = auth()->user();

        // Get upcoming classes
        $batchIds = $user->enrollments()->active()->pluck('batch_id')->filter();
        $enrollmentIds = $user->enrollments()->active()->whereNull('batch_id')->pluck('id');

        $upcomingClasses = \App\Models\ClassSession::where(function ($query) use ($batchIds, $enrollmentIds) {
            $query->whereIn('batch_id', $batchIds)
                ->orWhereIn('enrollment_id', $enrollmentIds);
        })
            ->where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->with(['teacher:id,name,avatar', 'batch:id,name,course_id', 'batch.course:id,title'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        // Get active enrollments
        $activeEnrollments = $user->enrollments()
            ->active()
            ->with(['course:id,title,slug,thumbnail', 'batch:id,name'])
            ->get();

        // Get stats
        $streak = $user->learningStreak;

        // Count only 'present' attendance
        $classesAttended = $user->attendances()->where('status', 'present')->count();

        return Inertia::render('Student/Dashboard', [
            'stats' => [
                'active_courses' => $activeEnrollments->count(),
                'classes_attended' => $classesAttended,
                'current_streak' => $streak?->current_streak ?? 0,
                'longest_streak' => $streak?->longest_streak ?? 0,
                'points' => $user->points ?? 0,
                'badges_count' => $user->badges()->count(),
            ],
            'upcomingClasses' => $upcomingClasses->map(fn($class) => [
                'id' => $class->id,
                'title' => $class->title,
                'course_title' => $class->batch?->course?->title ?? 'Private Class',
                'scheduled_at' => $class->scheduled_at->toISOString(),
                'duration_minutes' => $class->duration_minutes,
                'teacher' => $class->teacher ? [
                    'name' => $class->teacher->name,
                ] : null,
                'zoom_join_url' => $class->isStartingSoon() ? $class->zoom_join_url : null,
                'is_starting_soon' => $class->isStartingSoon(),
            ]),
            'activeEnrollments' => $activeEnrollments->map(fn($enrollment) => [
                'id' => $enrollment->id,
                'type' => $enrollment->type,
                'progress_percentage' => $enrollment->progress_percentage,
                'classes_attended' => $enrollment->classes_attended,
                'classes_total' => $enrollment->classes_total,
                'course' => $enrollment->course ? [
                    'title' => $enrollment->course->title,
                    'slug' => $enrollment->course->slug,
                ] : null,
                'batch' => $enrollment->batch ? [
                    'name' => $enrollment->batch->name,
                ] : null,
            ]),
            'recentBadges' => $user->badges()->latest()->limit(6)->get()->map(fn($badge) => [
                'id' => $badge->id,
                'name' => $badge->name,
                'icon' => $badge->icon,
            ]),
        ]);
    })->name('student.dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // My Courses / Enrollments
    Route::get('/enrollments', function () {
        $user = auth()->user();
        $enrollments = $user->enrollments()
            ->with(['course:id,title,slug,thumbnail', 'batch:id,name'])
            ->latest()
            ->get();

        return Inertia::render('Student/Enrollments', [
            'enrollments' => $enrollments->map(fn($e) => [
                'id' => $e->id,
                'type' => $e->type,
                'status' => $e->status,
                'progress_percentage' => $e->progress_percentage,
                'classes_attended' => $e->classes_attended,
                'classes_total' => $e->classes_total,
                'enrolled_date' => $e->created_at->format('M d, Y'),
                'course' => $e->course ? [
                    'title' => $e->course->title,
                    'slug' => $e->course->slug,
                ] : null,
                'batch' => $e->batch ? ['name' => $e->batch->name] : null,
            ]),
        ]);
    })->name('enrollments');

    // Enrollment Detail
    Route::get('/my-courses/{id}', function ($id) {
        $user = auth()->user();
        $enrollment = $user->enrollments()
            ->with(['course:id,title,slug', 'batch:id,name,teacher_id', 'batch.teacher:id,name'])
            ->findOrFail($id);

        // Get upcoming classes
        $upcomingClasses = \App\Models\ClassSession::where(function ($q) use ($enrollment) {
            $q->where('batch_id', $enrollment->batch_id)
                ->orWhere('enrollment_id', $enrollment->id);
        })
            ->where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get(['id', 'title', 'scheduled_at', 'duration_minutes', 'status']);

        // Get recent classes
        $recentClasses = \App\Models\ClassSession::where(function ($q) use ($enrollment) {
            $q->where('batch_id', $enrollment->batch_id)
                ->orWhere('enrollment_id', $enrollment->id);
        })
            ->where('scheduled_at', '<', now())
            ->orderBy('scheduled_at', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'scheduled_at', 'status']);

        return Inertia::render('Student/EnrollmentDetail', [
            'enrollment' => [
                'id' => $enrollment->id,
                'type' => $enrollment->type,
                'status' => $enrollment->status,
                'progress_percentage' => $enrollment->progress_percentage,
                'classes_attended' => $enrollment->classes_attended,
                'classes_total' => $enrollment->classes_total,
                'enrolled_date' => $enrollment->created_at->format('M d, Y'),
                'course' => $enrollment->course ? [
                    'title' => $enrollment->course->title,
                    'slug' => $enrollment->course->slug,
                ] : null,
                'batch' => $enrollment->batch ? ['name' => $enrollment->batch->name] : null,
                'teacher' => $enrollment->batch?->teacher ? ['name' => $enrollment->batch->teacher->name] : null,
            ],
            'upcomingClasses' => $upcomingClasses->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'scheduled_at' => $c->scheduled_at->toISOString(),
                'duration_minutes' => $c->duration_minutes,
                'status' => $c->status,
            ]),
            'recentClasses' => $recentClasses->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'scheduled_at' => $c->scheduled_at->toISOString(),
                'status' => $c->status,
            ]),
            'resources' => [],
        ]);
    })->name('enrollment.detail');

    Route::get('/classes', fn() => Inertia::render('Student/Classes'))->name('classes');
    Route::get('/resources', fn() => Inertia::render('Student/Resources'))->name('resources');

    // Recorded Classes
    Route::get('/recordings', function () {
        $user = auth()->user();

        // Get recordings from user's enrolled batches
        $enrolledBatchIds = $user->enrollments()->pluck('batch_id')->filter();

        $recordings = \App\Models\ClassSession::whereIn('batch_id', $enrolledBatchIds)
            ->whereNotNull('recording_url')
            ->where('status', 'completed')
            ->with(['batch:id,name,course_id', 'batch.course:id,title'])
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return Inertia::render('Student/Recordings', [
            'recordings' => $recordings->map(fn($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'recording_url' => $r->recording_url,
                'duration_minutes' => $r->duration_minutes,
                'recorded_at' => $r->scheduled_at->toISOString(),
                'course_id' => $r->batch?->course_id,
                'course' => $r->batch?->course ? ['id' => $r->batch->course->id, 'title' => $r->batch->course->title] : null,
            ]),
        ]);
    })->name('recordings');

    // Resources Download
    Route::get('/resources', function () {
        $user = auth()->user();
        $enrolledCourseIds = $user->enrollments()->pluck('course_id')->filter();

        $resources = \App\Models\Resource::whereIn('course_id', $enrolledCourseIds)
            ->orWhere('is_public', true)
            ->with('course:id,title')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Student/Resources', [
            'resources' => $resources->map(fn($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'description' => $r->description,
                'type' => $r->type,
                'file_size' => $r->file_size,
                'download_url' => $r->file_url,
                'course_id' => $r->course_id,
                'course' => $r->course ? ['id' => $r->course->id, 'title' => $r->course->title] : null,
                'created_at' => $r->created_at->toISOString(),
            ]),
        ]);
    })->name('resources');

    // Enrollment Flow
    Route::get('/enroll/{slug}', function ($slug) {
        $course = \App\Models\Course::where('slug', $slug)
            ->published()
            ->with([
                'batches' => function ($q) {
                    $q->whereIn('status', ['upcoming', 'active'])
                        ->where('is_accepting_enrollments', true)
                        ->with('teacher:id,name')
                        ->orderBy('start_date');
                },
            ])
            ->firstOrFail();

        return Inertia::render('Courses/Enroll', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'pricing' => [
                    'group' => $course->price_group,
                    'private' => $course->price_private,
                ],
            ],
            'batches' => $course->batches->map(fn($batch) => [
                'id' => $batch->id,
                'name' => $batch->name,
                'start_date' => $batch->start_date->format('M d, Y'),
                'formatted_schedule' => $batch->formatted_schedule,
                'available_slots' => $batch->available_slots,
                'teacher' => $batch->teacher ? ['name' => $batch->teacher->name] : null,
            ]),
        ]);
    })->name('enroll');

    // Leaderboard
    Route::get('/leaderboard', function () {
        $user = auth()->user();
        $timeframe = request('timeframe', 'all');

        $query = \App\Models\User::whereHas('enrollments')
            ->withSum('gamificationPoints as points', 'points');

        if ($timeframe === 'week') {
            $query->whereHas('gamificationPoints', fn($q) => $q->where('earned_at', '>=', now()->subWeek()));
        } elseif ($timeframe === 'month') {
            $query->whereHas('gamificationPoints', fn($q) => $q->where('earned_at', '>=', now()->subMonth()));
        }

        $leaderboard = $query->orderByDesc('points')
            ->limit(50)
            ->get()
            ->map(fn($u, $index) => [
                'id' => $u->id,
                'name' => $u->name,
                'points' => $u->points ?? 0,
                'level' => $u->gamification_level ?? 'Beginner',
            ]);

        $userRank = [
            'rank' => $leaderboard->search(fn($u) => $u['id'] === $user->id) + 1 ?: 'N/A',
            'points' => $user->gamificationPoints()->sum('points'),
            'classes_attended' => \App\Models\Attendance::where('student_id', $user->id)->where('status', 'present')->count(),
        ];

        $userBadges = $user->userBadges()->with('badge')->get()->map(fn($ub) => [
            'id' => $ub->badge->id,
            'name' => $ub->badge->name,
            'icon' => $ub->badge->icon,
        ]);

        $streakInfo = [
            'current' => $user->learningStreaks()->orderByDesc('current_streak')->value('current_streak') ?? 0,
        ];

        return Inertia::render('Leaderboard', [
            'leaderboard' => $leaderboard,
            'userRank' => $userRank,
            'timeframe' => $timeframe,
            'userBadges' => $userBadges,
            'streakInfo' => $streakInfo,
        ]);
    })->name('leaderboard');

    // Certificates List
    Route::get('/certificates', function () {
        $user = auth()->user();

        $certificates = \App\Models\Certificate::where('user_id', $user->id)
            ->with('course:id,title')
            ->orderBy('course_completed_at', 'desc')
            ->get();

        return Inertia::render('Student/Certificates', [
            'certificates' => $certificates->map(fn($cert) => [
                'id' => $cert->id,
                'certificate_number' => $cert->certificate_number,
                'course' => $cert->course ? ['title' => $cert->course->title] : null,
                'issued_at' => $cert->course_completed_at?->format('M d, Y') ?? 'N/A',
                'classes_attended' => $cert->metadata['classes_attended'] ?? 0,
            ]),
        ]);
    })->name('certificates.index');

    Route::post('/support', function () {
        $user = auth()->user();

        $validated = request()->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'priority' => 'nullable|in:low,normal,high',
        ]);

        \App\Models\SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'] ?? 'normal',
            'status' => 'open',
        ]);

        return back()->with('success', 'Support ticket created successfully!');
    })->name('support.store');

    // Certificate View
    Route::get('/certificates/{id}', function ($id) {
        $user = auth()->user();

        $certificate = \App\Models\Certificate::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['course:id,title', 'user:id,name'])
            ->firstOrFail();

        return Inertia::render('Certificate', [
            'certificate' => [
                'id' => $certificate->id,
                'certificate_id' => $certificate->certificate_number,
                'student_name' => $certificate->user->name,
                'course' => $certificate->course->title,
                'teacher_name' => $certificate->issued_by ?? 'QuranLearn Team',
                'completed_date' => $certificate->issued_at->format('F d, Y'),
                'classes_attended' => $certificate->classes_attended ?? 0,
                'duration_weeks' => $certificate->duration_weeks ?? 0,
                'xp_earned' => $certificate->xp_earned ?? 0,
            ],
        ]);
    })->name('certificate.show');

    // Notifications
    Route::get('/notifications', function () {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->data['type'] ?? 'announcement',
                'title' => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'action_url' => $n->data['action_url'] ?? null,
                'action_text' => $n->data['action_text'] ?? null,
                'created_at' => $n->created_at->toISOString(),
                'read_at' => $n->read_at?->toISOString(),
            ]);

        return Inertia::render('Notifications', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    })->name('notifications');

    Route::post('/notifications/{id}/read', function ($id) {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return back();
    })->name('notifications.read');

    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return back();
    })->name('notifications.markAllRead');

    // Notification Settings Page
    Route::get('/settings/notifications', function () {
        $user = auth()->user();

        // Get user's notification preferences (from user meta or settings table)
        $settings = [
            'class_reminders' => $user->notification_settings['class_reminders'] ?? true,
            'recitation_feedback' => $user->notification_settings['recitation_feedback'] ?? true,
            'weekly_progress' => $user->notification_settings['weekly_progress'] ?? true,
            'promotional' => $user->notification_settings['promotional'] ?? false,
            'reminder_time' => $user->notification_settings['reminder_time'] ?? 30,
        ];

        return Inertia::render('Student/NotificationSettings', [
            'settings' => $settings,
        ]);
    })->name('settings.notifications');

    // Save Notification Settings
    Route::post('/settings/notifications', function () {
        $validated = request()->validate([
            'class_reminders' => 'boolean',
            'recitation_feedback' => 'boolean',
            'weekly_progress' => 'boolean',
            'promotional' => 'boolean',
            'reminder_time' => 'integer|in:15,30,60,120,1440',
        ]);

        $user = auth()->user();
        $user->notification_settings = $validated;
        $user->save();

        return back()->with('success', 'Notification settings saved!');
    })->name('settings.notifications.update');

    // Profile Avatar Upload
    Route::post('/profile/avatar', function () {
        $validated = request()->validate([
            'avatar' => 'required|image|mimes:jpeg,png,gif|max:2048',
        ]);

        $user = auth()->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = request()->file('avatar')->store('avatars', 'public');

        // Update user
        $user->avatar = '/storage/' . $path;
        $user->save();

        return back()->with('success', 'Avatar updated successfully!');
    })->name('profile.avatar');

    // Submit Enrollment
    Route::post('/enrollments', function () {
        $validated = request()->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'type' => 'required|in:group,private',
        ]);

        $user = auth()->user();
        $course = \App\Models\Course::findOrFail($validated['course_id']);

        // Check if already enrolled
        $existingEnrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'pending'])
            ->first();

        if ($existingEnrollment) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        $amount = $validated['type'] === 'private' ? $course->price_private : $course->price_group;

        // Create enrollment
        $startDate = $validated['batch_id']
            ? \App\Models\Batch::find($validated['batch_id'])?->start_date
            : now();

        $enrollment = \App\Models\Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'batch_id' => $validated['batch_id'],
            'type' => $validated['type'],
            'status' => 'pending',
            'amount' => $amount,
            'start_date' => $startDate ?? now(),
            'enrolled_at' => now(),
        ]);

        // Create pending payment
        $payment = \App\Models\Payment::create([
            'user_id' => $user->id,
            'enrollment_id' => $enrollment->id,
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'pending',
            'gateway' => 'stripe', // Default, can be changed in checkout
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
        ]);

        // Redirect to checkout/payment page
        return redirect()->route('checkout', ['enrollment' => $enrollment->id])
            ->with('success', 'Enrollment created! Please complete payment.');
    })->name('enrollments.store');

    // Checkout Page
    Route::get('/checkout/{enrollment}', function ($enrollmentId) {
        $user = auth()->user();
        $enrollment = \App\Models\Enrollment::where('id', $enrollmentId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['course:id,title,slug', 'batch:id,name'])
            ->firstOrFail();

        return Inertia::render('Checkout', [
            'enrollment' => [
                'id' => $enrollment->id,
                'type' => $enrollment->type,
                'amount' => $enrollment->amount,
                'course' => $enrollment->course,
                'batch' => $enrollment->batch,
            ],
        ]);
    })->name('checkout');

    // Process Payment (simplified for now)
    Route::post('/checkout/{enrollment}/pay', function ($enrollmentId) {
        $user = auth()->user();
        $enrollment = \App\Models\Enrollment::where('id', $enrollmentId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Mark as paid (in real app, this would integrate with Stripe/SSLCommerz)
        $enrollment->update(['status' => 'active']);

        $enrollment->payments()->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Payment successful! You are now enrolled.');
    })->name('checkout.pay');

    // Student Review Submission
    Route::post('/reviews', function () {
        $user = auth()->user();

        $validated = request()->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:users,id',
            'enrollment_id' => 'nullable|exists:enrollments,id',
        ]);

        // Verify user is enrolled in this course
        $enrollment = $user->enrollments()
            ->where('course_id', $validated['course_id'])
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'You must be enrolled in this course to review it.');
        }

        // Check for existing review
        $existingReview = \App\Models\Review::where('student_id', $user->id)
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existingReview) {
            // Update existing review
            $existingReview->update([
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
                'status' => 'pending', // Re-submit for moderation
            ]);
            return back()->with('success', 'Your review has been updated and is pending approval.');
        }

        // Create new review
        \App\Models\Review::create([
            'student_id' => $user->id,
            'course_id' => $validated['course_id'],
            'teacher_id' => $validated['teacher_id'],
            'enrollment_id' => $validated['enrollment_id'] ?? $enrollment->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending',
            'is_visible' => false,
            'is_verified' => true, // Verified enrollment
        ]);

        return back()->with('success', 'Thank you for your review! It will be visible after approval.');
    })->name('reviews.store');

    // Skip review prompt
    Route::post('/enrollments/{id}/skip-review', function ($id) {
        $user = auth()->user();
        $enrollment = \App\Models\Enrollment::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $enrollment->update(['review_skipped_at' => now()]);

        return back();
    })->name('enrollments.skip-review');

    // Class Join Page
    Route::get('/classes/{id}/join', function ($id) {
        $user = auth()->user();

        $classSession = \App\Models\ClassSession::with([
            'teacher:id,name,avatar',
            'batch:id,name,course_id',
            'batch.course:id,title',
        ])->findOrFail($id);

        // Verify user has access to this class (enrolled in same batch - any valid status)
        $hasAccess = $user->enrollments()
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->where(function ($q) use ($classSession) {
                $q->where('batch_id', $classSession->batch_id)
                    ->orWhere('id', $classSession->enrollment_id);
            })
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this class.');
        }

        return Inertia::render('Student/JoinClass', [
            'classSession' => [
                'id' => $classSession->id,
                'title' => $classSession->title,
                'scheduled_at' => $classSession->scheduled_at->toISOString(),
                'duration_minutes' => $classSession->duration_minutes,
                'status' => $classSession->status,
                'zoom_join_url' => $classSession->zoom_join_url,
                'teacher' => $classSession->teacher ? [
                    'name' => $classSession->teacher->name,
                ] : null,
                'batch' => $classSession->batch ? [
                    'name' => $classSession->batch->name,
                    'course' => $classSession->batch->course ? [
                        'title' => $classSession->batch->course->title,
                    ] : null,
                ] : null,
            ],
        ]);
    })->name('class.join');

    // ==========================================
    // QURAN LEARNING ROUTES
    // ==========================================

    // Quran Reader
    Route::get('/quran', function () {
        $surahs = \App\Models\QuranSurah::orderBy('surah_number')->get();

        return Inertia::render('Student/QuranReader', [
            'surahs' => $surahs->map(fn($s) => [
                'id' => $s->id,
                'surah_number' => $s->surah_number,
                'name_arabic' => $s->name_arabic,
                'name_english' => $s->name_english,
                'name_bangla' => $s->name_bangla,
                'total_ayahs' => $s->total_ayahs,
                'revelation_type' => $s->revelation_type,
            ]),
        ]);
    })->name('quran');

    // Hifz Quran Reader (Para/Page based)
    Route::get('/hifz', function () {
        return Inertia::render('Student/HifzReader');
    })->name('hifz');

    // Get Surah Ayahs (API-like endpoint)
    Route::get('/quran/surah/{surahNumber}', function ($surahNumber) {
        $surah = \App\Models\QuranSurah::where('surah_number', $surahNumber)->firstOrFail();

        // Get progress for current user
        $progress = auth()->user()->surahProgress()->where('surah_id', $surah->id)->first();

        return response()->json([
            'surah' => [
                'id' => $surah->id,
                'surah_number' => $surah->surah_number,
                'name_arabic' => $surah->name_arabic,
                'name_english' => $surah->name_english,
                'name_bangla' => $surah->name_bangla,
                'total_ayahs' => $surah->total_ayahs,
            ],
            'progress' => $progress ? [
                'status' => $progress->status,
                'last_ayah_read' => $progress->last_ayah_read,
                'ayahs_memorized' => $progress->ayahs_memorized,
            ] : null,
        ]);
    })->name('quran.surah');

    // Update Surah Progress
    Route::post('/quran/progress/{surahId}', function ($surahId) {
        $validated = request()->validate([
            'last_ayah_read' => 'nullable|integer',
            'ayahs_memorized' => 'nullable|integer',
            'status' => 'nullable|in:not_started,in_progress,completed,memorized',
        ]);

        $user = auth()->user();
        $surah = \App\Models\QuranSurah::findOrFail($surahId);

        $progress = \App\Models\StudentSurahProgress::updateOrCreate(
            ['user_id' => $user->id, 'surah_id' => $surah->id],
            array_merge($validated, [
                'started_at' => now(),
                'read_count' => \DB::raw('read_count + 1'),
            ])
        );

        // Check if completed
        if (($validated['last_ayah_read'] ?? 0) >= $surah->total_ayahs) {
            $progress->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        return response()->json(['success' => true, 'progress' => $progress]);
    })->name('quran.progress.update');

    // Student Progress Dashboard
    Route::get('/progress', function () {
        $user = auth()->user();

        // Get all surahs with user progress
        $surahs = \App\Models\QuranSurah::with(['studentProgress' => fn($q) => $q->where('user_id', $user->id)])
            ->orderBy('surah_number')
            ->get();

        // Get tajweed skills
        $tajweedRules = \App\Models\TajweedRule::with(['studentSkills' => fn($q) => $q->where('user_id', $user->id)])
            ->get();

        // Get recent learning sessions
        $recentSessions = \App\Models\LearningSession::where('user_id', $user->id)
            ->orderBy('session_date', 'desc')
            ->limit(7)
            ->get();

        // Calculate stats
        $stats = [
            'surahs_started' => $surahs->filter(fn($s) => $s->studentProgress->first()?->status !== 'not_started')->count(),
            'surahs_completed' => $surahs->filter(fn($s) => in_array($s->studentProgress->first()?->status, ['completed', 'memorized']))->count(),
            'surahs_memorized' => $surahs->filter(fn($s) => $s->studentProgress->first()?->status === 'memorized')->count(),
            'total_ayahs_read' => $surahs->sum(fn($s) => $s->studentProgress->first()?->last_ayah_read ?? 0),
            'total_ayahs_memorized' => $surahs->sum(fn($s) => $s->studentProgress->first()?->ayahs_memorized ?? 0),
        ];

        return Inertia::render('Student/Progress', [
            'stats' => $stats,
            'surahs' => $surahs->map(fn($s) => [
                'id' => $s->id,
                'surah_number' => $s->surah_number,
                'name_arabic' => $s->name_arabic,
                'name_english' => $s->name_english,
                'total_ayahs' => $s->total_ayahs,
                'progress' => $s->studentProgress->first() ? [
                    'status' => $s->studentProgress->first()->status,
                    'percentage' => $s->studentProgress->first()->progress_percentage,
                    'memorized_percentage' => $s->studentProgress->first()->memorization_percentage,
                ] : null,
            ]),
            'tajweedSkills' => $tajweedRules->map(fn($r) => [
                'id' => $r->id,
                'name_arabic' => $r->name_arabic,
                'name_english' => $r->name_english,
                'name_bangla' => $r->name_bangla,
                'color_code' => $r->color_code,
                'skill_level' => $r->studentSkills->first()?->skill_level ?? 0,
            ]),
            'recentSessions' => $recentSessions->map(fn($s) => [
                'date' => $s->session_date->format('M d'),
                'ayahs_read' => $s->ayahs_read,
                'time_spent' => $s->time_spent_minutes,
            ]),
        ]);
    })->name('progress');

    // Recitation Submissions
    Route::get('/recitations', function () {
        $user = auth()->user();

        $submissions = \App\Models\RecitationSubmission::where('user_id', $user->id)
            ->with(['surah:id,surah_number,name_english,name_arabic', 'feedback.teacher:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        $surahs = \App\Models\QuranSurah::orderBy('surah_number')->get(['id', 'surah_number', 'name_english', 'name_arabic', 'total_ayahs']);

        return Inertia::render('Student/Recitations', [
            'submissions' => $submissions->map(fn($s) => [
                'id' => $s->id,
                'surah' => $s->surah ? [
                    'number' => $s->surah->surah_number,
                    'name' => $s->surah->name_english,
                ] : null,
                'ayah_range' => $s->ayah_range,
                'audio_url' => $s->audio_url,
                'status' => $s->status,
                'created_at' => $s->created_at->toISOString(),
                'feedback' => $s->feedback ? [
                    'overall_rating' => $s->feedback->overall_rating,
                    'average_score' => $s->feedback->average_score,
                    'feedback_text' => $s->feedback->feedback_text,
                    'teacher_name' => $s->feedback->teacher?->name,
                ] : null,
            ]),
            'surahs' => $surahs,
        ]);
    })->name('recitations');

    // Submit Recitation
    Route::post('/recitations', function () {
        $validated = request()->validate([
            'surah_id' => 'required|exists:quran_surahs,id',
            'ayah_from' => 'required|integer|min:1',
            'ayah_to' => 'required|integer|gte:ayah_from',
            'audio' => 'required|file|mimes:mp3,wav,m4a,webm|max:20480', // 20MB max
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $file = request()->file('audio');
        $path = $file->store('recitations/' . $user->id, 'public');

        $submission = \App\Models\RecitationSubmission::create([
            'user_id' => $user->id,
            'surah_id' => $validated['surah_id'],
            'ayah_from' => $validated['ayah_from'],
            'ayah_to' => $validated['ayah_to'],
            'audio_path' => $path,
            'duration_seconds' => null, // Could be calculated
            'status' => 'pending',
            'student_notes' => $validated['notes'],
        ]);

        return redirect()->route('recitations')->with('success', 'Recitation submitted successfully! Your teacher will review it soon.');
    })->name('recitations.store');
});

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:teacher'])->prefix('teacher')->group(function () {
    Route::get('/', function () {
        $user = auth()->user();
        $teacherProfile = $user->teacherProfile;

        // Get batches
        $batches = $user->taughtBatches()
            ->with(['course:id,title'])
            ->withCount('enrollments')
            ->get();

        // Get today's classes
        $todaysClasses = \App\Models\ClassSession::forTeacher($user->id)
            ->today()
            ->with(['batch:id,name,course_id', 'batch.course:id,title'])
            ->orderBy('scheduled_at')
            ->get();

        // Get upcoming classes (next 7 days, excluding today)
        $upcomingClasses = \App\Models\ClassSession::forTeacher($user->id)
            ->where('scheduled_at', '>', now()->endOfDay())
            ->where('scheduled_at', '<', now()->addDays(7))
            ->where('status', 'scheduled')
            ->with(['batch:id,name'])
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        // Get total students (sum from batch enrollments_count via withCount)
        $totalStudents = $batches->sum('enrollments_count');

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
    })->name('teacher.dashboard');

    // Dashboard alias
    Route::get('/dashboard', function () {
        return redirect()->route('teacher.dashboard');
    });

    // Teacher Batch Detail
    Route::get('/batches/{id}', function ($id) {
        $user = auth()->user();

        $batch = \App\Models\Batch::where('id', $id)
            ->where('teacher_id', $user->id)
            ->with(['course:id,title'])
            ->firstOrFail();

        $students = \App\Models\Enrollment::where('batch_id', $id)
            ->with('user:id,name,email')
            ->get()
            ->map(fn($e) => [
                'id' => $e->user->id,
                'name' => $e->user->name,
                'email' => $e->user->email,
                'enrolled_date' => $e->created_at->toDateString(),
                'progress' => $e->progress_percentage ?? 0,
                'attendance_rate' => 85, // Placeholder
            ]);

        $upcomingClasses = \App\Models\ClassSession::where('batch_id', $id)
            ->where('scheduled_at', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'scheduled_at' => $c->scheduled_at->toISOString(),
                'zoom_start_url' => $c->zoom_start_url,
            ]);

        $recentClasses = \App\Models\ClassSession::where('batch_id', $id)
            ->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'scheduled_at' => $c->scheduled_at->toISOString(),
            ]);

        return Inertia::render('Teacher/BatchDetail', [
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->name,
                'status' => $batch->status,
                'max_students' => $batch->max_students,
                'start_date' => $batch->start_date?->toDateString(),
                'end_date' => $batch->end_date?->toDateString(),
                'schedule' => $batch->schedule,
                'course' => $batch->course ? ['title' => $batch->course->title] : null,
            ],
            'students' => $students,
            'upcomingClasses' => $upcomingClasses,
            'recentClasses' => $recentClasses,
        ]);
    })->name('teacher.batch.detail');

    // Teacher Batches Listing
    Route::get('/batches', function () {
        $user = auth()->user();

        $batches = \App\Models\Batch::where('teacher_id', $user->id)
            ->with(['course:id,title'])
            ->withCount('enrollments')
            ->orderBy('start_date', 'desc')
            ->get();

        // Sum enrollments_count from batches (from withCount)
        $totalStudents = $batches->sum('enrollments_count');

        return Inertia::render('Teacher/Batches', [
            'batches' => $batches->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'status' => $b->status,
                'enrolled_students' => $b->enrollments_count,
                'course' => $b->course ? ['title' => $b->course->title] : null,
            ]),
            'stats' => [
                'total_batches' => $batches->count(),
                'active_batches' => $batches->where('status', 'active')->count(),
                'total_students' => $totalStudents,
            ],
        ]);
    })->name('teacher.batches');

    // Attendance List - Shows all classes for attendance
    Route::get('/attendance', function () {
        $user = auth()->user();

        $batchIds = \App\Models\Batch::where('teacher_id', $user->id)->pluck('id');

        $classes = \App\Models\ClassSession::whereIn('batch_id', $batchIds)
            ->with(['batch:id,name'])
            ->orderBy('scheduled_at', 'desc')
            ->limit(30)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'scheduled_at' => $c->scheduled_at->toISOString(),
                'status' => $c->status,
                'batch' => $c->batch,
                'attendance_marked' => \App\Models\Attendance::where('class_id', $c->id)->exists(),
            ]);

        return Inertia::render('Teacher/AttendanceList', [
            'classes' => $classes,
        ]);
    })->name('teacher.attendance.list');

    // Create Class Session Page - MUST be before /classes/{id} routes!
    Route::get('/classes/create', function () {
        $user = auth()->user();

        $batches = \App\Models\Batch::where('teacher_id', $user->id)
            ->where('status', 'active')
            ->with('course:id,title')
            ->withCount('enrollments')
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'course' => $b->course,
                'enrolled_students' => $b->enrollments_count,
            ]);

        return Inertia::render('Teacher/CreateClass', [
            'batches' => $batches,
            'teacherTimezone' => $user->timezone ?? 'Asia/Dhaka',
        ]);
    })->name('teacher.classes.create');

    // Store Class Session
    Route::post('/classes', function () {
        $user = auth()->user();

        $validated = request()->validate([
            'batch_id' => 'required|exists:batches,id',
            'title' => 'required|string|max:255',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'description' => 'nullable|string',
            'zoom_start_url' => 'nullable|url',
            'zoom_join_url' => 'nullable|url',
        ]);

        // Verify teacher owns this batch
        $batch = \App\Models\Batch::where('id', $validated['batch_id'])
            ->where('teacher_id', $user->id)
            ->firstOrFail();

        // Combine date and time in teacher's timezone, then convert to UTC for storage
        $teacherTimezone = $user->timezone ?? 'Asia/Dhaka';
        $scheduledAt = \Carbon\Carbon::parse(
            $validated['scheduled_date'] . ' ' . $validated['scheduled_time'],
            $teacherTimezone
        )->utc();

        $classSession = \App\Models\ClassSession::create([
            'batch_id' => $batch->id,
            'teacher_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $validated['duration_minutes'],
            'status' => 'scheduled',
            'zoom_start_url' => $validated['zoom_start_url'],
            'zoom_join_url' => $validated['zoom_join_url'],
        ]);

        return redirect()->route('teacher.schedule')
            ->with('success', 'Class session created successfully!');
    })->name('teacher.classes.store');

    // Attendance Page
    Route::get('/classes/{id}/attendance', function ($id) {
        $user = auth()->user();

        $classSession = \App\Models\ClassSession::where('id', $id)
            ->whereHas('batch', fn($q) => $q->where('teacher_id', $user->id))
            ->with(['batch:id,name,course_id', 'batch.course:id,title'])
            ->firstOrFail();

        // Get enrollments - include all non-cancelled enrollments
        $students = \App\Models\Enrollment::where('batch_id', $classSession->batch_id)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->with('user:id,name,email,avatar')
            ->get()
            ->map(fn($e) => [
                'id' => $e->user->id,
                'name' => $e->user->name,
                'email' => $e->user->email,
                'avatar' => $e->user->avatar,
            ]);

        // Get existing attendance records
        $existingAttendance = \App\Models\Attendance::where('class_id', $id)
            ->pluck('status', 'student_id')
            ->toArray();

        return Inertia::render('Teacher/Attendance', [
            'classSession' => [
                'id' => $classSession->id,
                'title' => $classSession->title,
                'date' => $classSession->scheduled_at->format('M d, Y'),
                'time' => $classSession->scheduled_at->format('g:i A'),
                'duration_minutes' => $classSession->duration_minutes,
                'batch' => $classSession->batch ? ['name' => $classSession->batch->name] : null,
                'course' => $classSession->batch?->course ? ['title' => $classSession->batch->course->title] : null,
            ],
            'students' => $students,
            'existingAttendance' => $existingAttendance,
        ]);
    })->name('teacher.attendance');

    // Submit Attendance
    Route::post('/classes/{id}/attendance', function ($id) {
        $user = auth()->user();

        $classSession = \App\Models\ClassSession::where('id', $id)
            ->whereHas('batch', fn($q) => $q->where('teacher_id', $user->id))
            ->firstOrFail();

        $validated = request()->validate([
            'attendance' => 'required|array',
            'class_summary' => 'nullable|string',
            'topics_covered' => 'nullable|string',
        ]);

        \Illuminate\Support\Facades\Log::info('Attendance ID: ' . $id);
        \Illuminate\Support\Facades\Log::info('Attendance Data: ' . json_encode($validated['attendance']));

        try {
            // Save attendance records and award XP
            $gamificationService = app(\App\Services\GamificationService::class);

            foreach ($validated['attendance'] as $userId => $status) {
                \Illuminate\Support\Facades\Log::info("Processing user $userId with status $status");

                \App\Models\Attendance::updateOrCreate(
                    [
                        'class_id' => $classSession->id,
                        'student_id' => $userId,
                    ],
                    [
                        'status' => $status,
                        'marked_by' => $user->id,
                        'marked_at' => now(),
                    ]
                );

                // Award XP for present students
                if ($status === 'present') {
                    $student = \App\Models\User::find($userId);
                    if ($student) {
                        $gamificationService->awardClassAttendance($student, $classSession->id);

                        // Update Streak
                        $streak = $student->learningStreak()->firstOrCreate([
                            'user_id' => $student->id
                        ], [
                            'current_streak' => 0,
                            'longest_streak' => 0,
                            'last_activity_date' => null
                        ]);

                        $streak->recordActivity();
                    }
                }
            }

            // Update class session
            $classSession->update([
                'summary' => $validated['class_summary'],
                'topics_covered' => $validated['topics_covered'],
                'status' => 'completed',
            ]);

            return redirect()->route('teacher.dashboard')
                ->with('success', 'Attendance saved and XP awarded!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Attendance Save Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to save attendance: ' . $e->getMessage()]);
        }
    })->name('teacher.attendance.store');

    // Teacher Resources
    Route::get('/resources', function () {
        $user = auth()->user();

        $courseIds = \App\Models\Batch::where('teacher_id', $user->id)->pluck('course_id')->unique();

        $resources = \App\Models\Resource::where('uploaded_by', $user->id)
            ->with('course:id,title')
            ->orderBy('created_at', 'desc')
            ->get();

        $courses = \App\Models\Course::whereIn('id', $courseIds)->select('id', 'title')->get();

        return Inertia::render('Teacher/Resources', [
            'resources' => $resources->map(fn($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'description' => $r->description,
                'type' => $r->type,
                'file_size' => $r->file_size,
                'file_url' => $r->file_url,
                'is_public' => $r->is_public,
                'course' => $r->course,
                'created_at' => $r->created_at->toISOString(),
            ]),
            'courses' => $courses,
        ]);
    })->name('teacher.resources');

    Route::post('/resources', function () {
        $user = auth()->user();

        $validated = request()->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:pdf,video,audio,document,image',
            'file' => 'required|file|max:51200', // 50MB max
            'is_public' => 'boolean',
        ]);

        $file = request()->file('file');
        $path = $file->store('resources', 'public');

        \App\Models\Resource::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'course_id' => $validated['course_id'],
            'type' => $validated['type'],
            'file_url' => '/storage/' . $path,
            'file_size' => $file->getSize(),
            'is_public' => $validated['is_public'] ?? false,
            'uploaded_by' => $user->id,
        ]);

        return redirect()->route('teacher.resources')->with('success', 'Resource uploaded successfully!');
    })->name('teacher.resources.store');

    Route::delete('/resources/{id}', function ($id) {
        $user = auth()->user();

        $resource = \App\Models\Resource::where('id', $id)
            ->where('uploaded_by', $user->id)
            ->firstOrFail();

        $resource->delete();

        return redirect()->route('teacher.resources')->with('success', 'Resource deleted successfully!');
    })->name('teacher.resources.destroy');

    // Student Notes
    Route::get('/notes', function () {
        $user = auth()->user();

        $batchIds = \App\Models\Batch::where('teacher_id', $user->id)->pluck('id');
        $batches = \App\Models\Batch::where('teacher_id', $user->id)->select('id', 'name')->get();

        $enrollments = \App\Models\Enrollment::whereIn('batch_id', $batchIds)
            ->active()
            ->with(['user:id,name,email', 'batch:id,name'])
            ->get();

        $students = $enrollments->map(fn($e) => [
            'id' => $e->user->id,
            'enrollment_id' => $e->id,
            'name' => $e->user->name,
            'email' => $e->user->email,
            'batch_id' => $e->batch_id,
            'batch_name' => $e->batch?->name,
            'progress' => $e->progress_percentage ?? 0,
            'teacher_note' => $e->teacher_notes,
            'note_type' => $e->note_type,
        ]);

        return Inertia::render('Teacher/StudentNotes', [
            'students' => $students,
            'batches' => $batches,
        ]);
    })->name('teacher.notes');

    Route::post('/students/{id}/notes', function ($id) {
        $validated = request()->validate([
            'note' => 'nullable|string',
            'type' => 'required|in:general,progress,concern,followup',
        ]);

        // Find enrollment for this student in teacher's batches
        $user = auth()->user();
        $batchIds = \App\Models\Batch::where('teacher_id', $user->id)->pluck('id');

        $enrollment = \App\Models\Enrollment::where('user_id', $id)
            ->whereIn('batch_id', $batchIds)
            ->firstOrFail();

        $enrollment->update([
            'teacher_notes' => $validated['note'],
            'note_type' => $validated['type'],
        ]);

        return back()->with('success', 'Note saved successfully!');
    })->name('teacher.notes.store');

    // Teacher Schedule
    Route::get('/schedule', function () {
        $user = auth()->user();

        $classes = \App\Models\ClassSession::forTeacher($user->id)
            ->where('scheduled_at', '>=', now()->startOfWeek())
            ->where('scheduled_at', '<=', now()->endOfWeek()->addWeeks(2))
            ->with(['batch:id,name,course_id', 'batch.course:id,title'])
            ->orderBy('scheduled_at')
            ->get();

        return Inertia::render('Teacher/Schedule', [
            'classes' => $classes->map(fn($class) => [
                'id' => $class->id,
                'title' => $class->title,
                'scheduled_at' => $class->scheduled_at->toISOString(),
                'duration_minutes' => $class->duration_minutes,
                'status' => $class->status,
                'batch' => $class->batch ? [
                    'name' => $class->batch->name,
                    'course' => $class->batch->course ? ['title' => $class->batch->course->title] : null,
                ] : null,
                'zoom_start_url' => $class->zoom_start_url,
            ]),
        ]);
    })->name('teacher.schedule');

    // Teacher Students List
    Route::get('/students', function () {
        $user = auth()->user();

        $batchIds = \App\Models\Batch::where('teacher_id', $user->id)->pluck('id');
        $batches = \App\Models\Batch::where('teacher_id', $user->id)
            ->select('id', 'name')
            ->withCount('enrollments')
            ->get();

        $students = \App\Models\Enrollment::whereIn('batch_id', $batchIds)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->with(['user:id,name,email,avatar', 'batch:id,name'])
            ->get()
            ->map(fn($e) => [
                'id' => $e->user->id,
                'enrollment_id' => $e->id,
                'name' => $e->user->name,
                'email' => $e->user->email,
                'avatar' => $e->user->avatar,
                'batch_id' => $e->batch_id,
                'batch_name' => $e->batch?->name,
                'enrolled_at' => $e->created_at->toDateString(),
                'progress' => $e->progress_percentage ?? 0,
                'attendance_rate' => 85, // Placeholder
            ]);

        return Inertia::render('Teacher/Students', [
            'students' => $students,
            'batches' => $batches,
            'stats' => [
                'total_students' => $students->count(),
                'total_batches' => $batches->count(),
            ],
        ]);
    })->name('teacher.students');

    // Student Progress Page
    Route::get('/students/{enrollmentId}/progress', function ($enrollmentId) {
        $user = auth()->user();

        $enrollment = \App\Models\Enrollment::where('id', $enrollmentId)
            ->whereHas('batch', fn($q) => $q->where('teacher_id', $user->id))
            ->with(['user:id,name,email,avatar', 'course:id,title', 'batch:id,name'])
            ->firstOrFail();

        // Get student's skill progress (stored in metadata or separate table)
        $skills = json_decode($enrollment->skill_progress ?? '{}', true);

        return Inertia::render('Teacher/StudentProgress', [
            'student' => [
                'id' => $enrollment->user->id,
                'name' => $enrollment->user->name,
                'email' => $enrollment->user->email,
                'avatar' => $enrollment->user->avatar,
            ],
            'enrollment' => [
                'id' => $enrollment->id,
                'course' => $enrollment->course,
                'batch' => $enrollment->batch,
                'progress_percentage' => $enrollment->progress_percentage,
            ],
            'skills' => [
                'quran_reading' => $skills['quran_reading'] ?? 0,
                'tajweed' => $skills['tajweed'] ?? 0,
                'pronunciation' => $skills['pronunciation'] ?? 0,
                'memorization' => $skills['memorization'] ?? 0,
                'fluency' => $skills['fluency'] ?? 0,
                'notes' => $skills['notes'] ?? '',
            ],
        ]);
    })->name('teacher.student.progress');

    // Update Student Progress
    Route::post('/students/{enrollmentId}/progress', function ($enrollmentId) {
        $user = auth()->user();

        $enrollment = \App\Models\Enrollment::where('id', $enrollmentId)
            ->whereHas('batch', fn($q) => $q->where('teacher_id', $user->id))
            ->firstOrFail();

        $validated = request()->validate([
            'quran_reading' => 'required|integer|min:0|max:100',
            'tajweed' => 'required|integer|min:0|max:100',
            'pronunciation' => 'required|integer|min:0|max:100',
            'memorization' => 'required|integer|min:0|max:100',
            'fluency' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        // Calculate overall progress
        $overallProgress = round(
            ($validated['quran_reading'] * 0.25) +
            ($validated['tajweed'] * 0.25) +
            ($validated['pronunciation'] * 0.20) +
            ($validated['memorization'] * 0.20) +
            ($validated['fluency'] * 0.10)
        );

        // Store skills as JSON
        $enrollment->update([
            'progress_percentage' => $overallProgress,
            'skill_progress' => json_encode($validated),
        ]);

        return redirect()->route('teacher.students')
            ->with('success', 'Student progress updated successfully!');
    })->name('teacher.student.progress.update');

    // Teacher Earnings
    Route::get('/earnings', function () {
        $user = auth()->user();
        $teacherProfile = $user->teacherProfile;

        // Get completed classes this month
        $classesThisMonth = \App\Models\ClassSession::forTeacher($user->id)
            ->where('status', 'completed')
            ->where('scheduled_at', '>=', now()->startOfMonth())
            ->count();

        // Calculate earnings (placeholder logic)
        $ratePerClass = $teacherProfile?->rate_per_class ?? 500; // Default BDT 500 per class
        $totalEarnings = $classesThisMonth * $ratePerClass;

        // Get monthly earnings history
        $monthlyEarnings = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $classes = \App\Models\ClassSession::forTeacher($user->id)
                ->where('status', 'completed')
                ->whereYear('scheduled_at', $month->year)
                ->whereMonth('scheduled_at', $month->month)
                ->count();
            $monthlyEarnings->push([
                'month' => $month->format('M Y'),
                'classes' => $classes,
                'earnings' => $classes * $ratePerClass,
            ]);
        }

        return Inertia::render('Teacher/Earnings', [
            'stats' => [
                'total_earnings' => $totalEarnings,
                'classes_this_month' => $classesThisMonth,
                'rate_per_class' => $ratePerClass,
                'pending_payout' => $totalEarnings,
            ],
            'monthlyEarnings' => $monthlyEarnings,
        ]);
    })->name('teacher.earnings');

    // Teacher Attendance Overview
    Route::get('/attendance', function () {
        $user = auth()->user();

        $batchIds = \App\Models\Batch::where('teacher_id', $user->id)->pluck('id');

        // Get recent classes that need attendance
        $recentClasses = \App\Models\ClassSession::whereIn('batch_id', $batchIds)
            ->where('scheduled_at', '<', now())
            ->orderBy('scheduled_at', 'desc')
            ->limit(20)
            ->with(['batch:id,name'])
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'scheduled_at' => $c->scheduled_at->toISOString(),
                'status' => $c->status,
                'batch' => $c->batch ? ['name' => $c->batch->name] : null,
                'attendance_marked' => $c->status === 'completed',
            ]);

        return Inertia::render('Teacher/AttendanceList', [
            'classes' => $recentClasses,
        ]);
    })->name('teacher.attendance.list');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        $stats = [
            'total_students' => \App\Models\User::role('student')->count(),
            'total_teachers' => \App\Models\User::role('teacher')->count(),
            'total_courses' => \App\Models\Course::published()->count(),
            'active_enrollments' => \App\Models\Enrollment::active()->count(),
            'revenue_this_month' => \App\Models\Payment::completed()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'pending_tickets' => \App\Models\SupportTicket::whereIn('status', ['open', 'pending'])->count(),
        ];

        $recentEnrollments = \App\Models\Enrollment::with(['user:id,name,email', 'course:id,title'])
            ->latest()
            ->limit(10)
            ->get();

        $topCourses = \App\Models\Course::published()
            ->orderBy('total_enrollments', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'price_group', 'total_enrollments']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentEnrollments' => $recentEnrollments->map(fn($e) => [
                'id' => $e->id,
                'user' => $e->user ? ['name' => $e->user->name] : null,
                'course' => $e->course ? ['title' => $e->course->title] : null,
                'type' => $e->type,
                'amount' => $e->amount,
                'created_at' => $e->created_at->toISOString(),
            ]),
            'topCourses' => $topCourses->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'price_group' => $c->price_group,
                'total_enrollments' => $c->total_enrollments,
            ]),
        ]);
    })->name('admin.dashboard');

    // Admin Teachers Management
    Route::get('/teachers', function () {
        $query = \App\Models\User::role('teacher')->with('teacherProfile');

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (request('status')) {
            $query->whereHas('teacherProfile', fn($q) => $q->where('status', request('status')));
        }

        $teachers = $query->latest()->paginate(15);

        return Inertia::render('Admin/Teachers/Index', [
            'teachers' => $teachers->through(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'email' => $t->email,
                'avatar' => $t->avatar,
                'created_at' => $t->created_at->toISOString(),
                'status' => $t->teacherProfile?->status ?? 'pending',
                'total_students' => $t->teacherProfile?->total_students ?? 0,
                'rating' => $t->teacherProfile?->average_rating ?? 0,
                'batches_count' => \App\Models\Batch::where('teacher_id', $t->id)->count(),
            ]),
            'filters' => request()->only(['search', 'status']),
        ]);
    })->name('admin.teachers.index');

    Route::post('/teachers/{id}/approve', function ($id) {
        $teacher = \App\Models\User::findOrFail($id);
        $teacher->teacherProfile()->updateOrCreate(
            ['user_id' => $id],
            ['status' => 'approved']
        );
        return back()->with('success', 'Teacher approved successfully.');
    })->name('admin.teachers.approve');

    Route::post('/teachers/{id}/reject', function ($id) {
        $teacher = \App\Models\User::findOrFail($id);
        $teacher->teacherProfile()->update(['status' => 'rejected']);
        return back()->with('success', 'Teacher rejected.');
    })->name('admin.teachers.reject');

    // Admin Enrollments Management
    Route::get('/enrollments', function () {
        $query = \App\Models\Enrollment::with(['user:id,name,email', 'course:id,title', 'batch:id,name']);

        if (request('search')) {
            $search = request('search');
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('course', fn($q) => $q->where('title', 'like', "%{$search}%"));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $enrollments = $query->latest()->paginate(20);

        $stats = [
            'total' => \App\Models\Enrollment::count(),
            'active' => \App\Models\Enrollment::where('status', 'active')->count(),
            'pending' => \App\Models\Enrollment::where('status', 'pending')->count(),
            'completed' => \App\Models\Enrollment::where('status', 'completed')->count(),
        ];

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $enrollments->through(fn($e) => [
                'id' => $e->id,
                'user' => $e->user ? ['id' => $e->user->id, 'name' => $e->user->name, 'email' => $e->user->email] : null,
                'course' => $e->course ? ['id' => $e->course->id, 'title' => $e->course->title] : null,
                'batch' => $e->batch ? ['id' => $e->batch->id, 'name' => $e->batch->name] : null,
                'type' => $e->type,
                'status' => $e->status,
                'amount' => $e->amount,
                'progress' => $e->progress_percentage ?? 0,
                'created_at' => $e->created_at->toISOString(),
            ]),
            'stats' => $stats,
            'filters' => request()->only(['search', 'status']),
        ]);
    })->name('admin.enrollments.index');

    Route::post('/enrollments/{id}/activate', function ($id) {
        \App\Models\Enrollment::where('id', $id)->update(['status' => 'active']);
        return back()->with('success', 'Enrollment activated.');
    })->name('admin.enrollments.activate');

    Route::delete('/enrollments/{id}', function ($id) {
        \App\Models\Enrollment::where('id', $id)->delete();
        return back()->with('success', 'Enrollment deleted.');
    })->name('admin.enrollments.delete');

    // Admin Course Management
    Route::get('/courses', function () {
        $query = \App\Models\Course::query();

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $courses = $query->latest()->paginate(15);

        return Inertia::render('Admin/Courses/Index', [
            'courses' => [
                'data' => $courses->map(fn($c) => [
                    'id' => $c->id,
                    'title' => $c->title,
                    'slug' => $c->slug,
                    'category' => $c->category,
                    'level' => $c->level,
                    'price_group' => $c->price_group,
                    'status' => $c->status,
                    'total_enrollments' => $c->total_enrollments,
                ]),
                'meta' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'total' => $courses->total(),
                ],
            ],
            'filters' => request()->only(['search']),
        ]);
    })->name('admin.courses.index');

    Route::get('/courses/create', function () {
        return Inertia::render('Admin/Courses/Form');
    })->name('admin.courses.create');

    Route::get('/courses/{id}/edit', function ($id) {
        $course = \App\Models\Course::findOrFail($id);
        return Inertia::render('Admin/Courses/Form', [
            'course' => $course,
        ]);
    })->name('admin.courses.edit');

    Route::post('/courses', function () {
        $validated = request()->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug',
            'short_description' => 'required|string|max:500',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'level' => 'required|string',
            'duration_weeks' => 'required|integer|min:1',
            'classes_per_week' => 'required|integer|min:1|max:7',
            'class_duration_minutes' => 'required|integer|min:15',
            'price_group' => 'required|numeric|min:0',
            'price_private' => 'required|numeric|min:0',
            'syllabus' => 'nullable|array',
            'learning_outcomes' => 'nullable|array',
            'requirements' => 'nullable|array',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        \App\Models\Course::create($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    })->name('admin.courses.store');

    Route::put('/courses/{id}', function ($id) {
        $course = \App\Models\Course::findOrFail($id);

        $validated = request()->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug,' . $id,
            'short_description' => 'required|string|max:500',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'level' => 'required|string',
            'duration_weeks' => 'required|integer|min:1',
            'classes_per_week' => 'required|integer|min:1|max:7',
            'class_duration_minutes' => 'required|integer|min:15',
            'price_group' => 'required|numeric|min:0',
            'price_private' => 'required|numeric|min:0',
            'syllabus' => 'nullable|array',
            'learning_outcomes' => 'nullable|array',
            'requirements' => 'nullable|array',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    })->name('admin.courses.update');

    Route::delete('/courses/{id}', function ($id) {
        $course = \App\Models\Course::findOrFail($id);
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    })->name('admin.courses.destroy');

    // Admin Class Session Management
    Route::get('/classes', function () {
        $query = \App\Models\ClassSession::with(['teacher:id,name', 'batch:id,name']);

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('teacher', fn($jq) => $jq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('batch', fn($jq) => $jq->where('name', 'like', "%{$search}%"));
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('date')) {
            $query->whereDate('scheduled_at', request('date'));
        }

        $classes = $query->orderBy('scheduled_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Classes/Index', [
            'classes' => $classes->through(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'teacher' => $c->teacher ? ['name' => $c->teacher->name] : null,
                'batch' => $c->batch ? ['name' => $c->batch->name] : null,
                'scheduled_at' => $c->scheduled_at->toISOString(),
                'status' => $c->status,
                'duration_minutes' => $c->duration_minutes,
                'attendee_count' => \App\Models\Attendance::where('class_id', $c->id)->where('status', 'present')->count(),
                'zoom_meeting_id' => $c->zoom_meeting_id
            ]),
            'filters' => request()->only(['search', 'status', 'date']),
        ]);
    })->name('admin.classes.index');

    Route::delete('/classes/{id}', function ($id) {
        $class = \App\Models\ClassSession::findOrFail($id);
        $class->delete();
        return back()->with('success', 'Class session deleted successfully.');
    })->name('admin.classes.destroy');

    // Admin User Management
    Route::get('/users', function () {
        $query = \App\Models\User::with('roles');

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (request('role')) {
            $query->role(request('role'));
        }

        $users = $query->latest()->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => [
                'data' => $users->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'roles' => $u->roles->pluck('name')->toArray(),
                    'is_active' => $u->is_active,
                    'created_at' => $u->created_at->toISOString(),
                    'last_login_at' => $u->last_login_at?->toISOString(),
                ]),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'total' => $users->total(),
                ],
            ],
            'filters' => request()->only(['search', 'role']),
        ]);
    })->name('admin.users.index');

    Route::delete('/users/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);

        // Don't allow deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    })->name('admin.users.destroy');

    // Create User Form
    Route::get('/users/create', function () {
        return Inertia::render('Admin/Users/Create');
    })->name('admin.users.create');

    Route::post('/users', function () {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:student,teacher,admin',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    })->name('admin.users.store');

    // ==========================================
    // ADMIN CHAT MANAGEMENT
    // ==========================================
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'adminIndex'])->name('admin.chat.index');
    Route::get('/chat/{id}', [\App\Http\Controllers\ChatController::class, 'adminShow'])->name('admin.chat.show');
    Route::post('/chat/{id}/reply', [\App\Http\Controllers\ChatController::class, 'adminReply'])->name('admin.chat.reply');
    Route::post('/chat/{id}/close', [\App\Http\Controllers\ChatController::class, 'closeConversation'])->name('admin.chat.close');
    Route::get('/chat/{id}/messages', [\App\Http\Controllers\ChatController::class, 'adminGetMessages'])->name('admin.chat.messages');

    // Reports / Analytics
    Route::get('/reports', function () {
        return redirect('/admin/analytics');
    })->name('admin.reports');

    // Admin Batch Management
    Route::get('/batches', function () {
        $query = \App\Models\Batch::with(['course:id,title', 'teacher:id,name'])
            ->withCount('enrollments');

        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $batches = $query->latest()->paginate(15);

        return Inertia::render('Admin/Batches/Index', [
            'batches' => [
                'data' => $batches->map(fn($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'course' => $b->course ? ['title' => $b->course->title] : null,
                    'teacher' => $b->teacher ? ['name' => $b->teacher->name] : null,
                    'start_date' => $b->start_date?->toDateString(),
                    'max_students' => $b->max_students,
                    'enrolled_count' => $b->enrollments_count,
                    'status' => $b->status,
                    'formatted_schedule' => $b->formatted_schedule,
                ]),
                'meta' => [
                    'current_page' => $batches->currentPage(),
                    'last_page' => $batches->lastPage(),
                    'total' => $batches->total(),
                ],
            ],
            'filters' => request()->only(['search', 'status']),
        ]);
    })->name('admin.batches.index');

    Route::get('/batches/create', function () {
        return Inertia::render('Admin/Batches/Form', [
            'courses' => \App\Models\Course::published()->get(['id', 'title']),
            'teachers' => \App\Models\User::role('teacher')->get(['id', 'name']),
        ]);
    })->name('admin.batches.create');

    Route::get('/batches/{id}/edit', function ($id) {
        $batch = \App\Models\Batch::findOrFail($id);
        return Inertia::render('Admin/Batches/Form', [
            'batch' => $batch,
            'courses' => \App\Models\Course::published()->get(['id', 'title']),
            'teachers' => \App\Models\User::role('teacher')->get(['id', 'name']),
        ]);
    })->name('admin.batches.edit');

    Route::post('/batches', function () {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_students' => 'required|integer|min:1|max:50',
            'schedule' => 'nullable|array',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'is_accepting_enrollments' => 'boolean',
        ]);

        \App\Models\Batch::create($validated);

        return redirect()->route('admin.batches.index')->with('success', 'Batch created successfully.');
    })->name('admin.batches.store');

    Route::put('/batches/{id}', function ($id) {
        $batch = \App\Models\Batch::findOrFail($id);

        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_students' => 'required|integer|min:1|max:50',
            'schedule' => 'nullable|array',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'is_accepting_enrollments' => 'boolean',
        ]);

        $batch->update($validated);

        return redirect()->route('admin.batches.index')->with('success', 'Batch updated successfully.');
    })->name('admin.batches.update');

    Route::delete('/batches/{id}', function ($id) {
        $batch = \App\Models\Batch::findOrFail($id);
        $batch->delete();

        return redirect()->route('admin.batches.index')->with('success', 'Batch deleted successfully.');
    })->name('admin.batches.destroy');

    // Admin Payment Management
    Route::get('/payments', function () {
        $query = \App\Models\Payment::with([
            'user:id,name,email',
            'enrollment:id,course_id',
            'enrollment.course:id,title'
        ]);

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('date_range')) {
            switch (request('date_range')) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        $payments = $query->latest()->paginate(20);

        $stats = [
            'total_revenue' => \App\Models\Payment::completed()->sum('amount'),
            'monthly_revenue' => \App\Models\Payment::completed()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'pending_amount' => \App\Models\Payment::where('status', 'pending')->sum('amount'),
            'total_transactions' => \App\Models\Payment::count(),
        ];

        return Inertia::render('Admin/Payments/Index', [
            'payments' => [
                'data' => $payments->map(fn($p) => [
                    'id' => $p->id,
                    'transaction_id' => $p->transaction_id,
                    'user' => $p->user ? [
                        'name' => $p->user->name,
                        'email' => $p->user->email,
                    ] : null,
                    'enrollment' => $p->enrollment ? [
                        'course' => $p->enrollment->course ? ['title' => $p->enrollment->course->title] : null,
                    ] : null,
                    'gateway' => $p->gateway,
                    'amount' => $p->amount,
                    'status' => $p->status,
                    'paid_at' => $p->paid_at?->toISOString(),
                    'created_at' => $p->created_at->toISOString(),
                ]),
                'meta' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'total' => $payments->total(),
                ],
            ],
            'stats' => $stats,
            'filters' => request()->only(['search', 'status', 'date_range']),
        ]);
    })->name('admin.payments.index');

    // Admin Settings
    Route::get('/settings', function () {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return Inertia::render('Admin/Settings', [
            'settings' => $settings,
        ]);
    })->name('admin.settings');

    // Admin Analytics
    Route::get('/analytics', function () {
        // Revenue stats
        $totalRevenue = \App\Models\Payment::where('status', 'completed')->sum('amount');
        $lastMonthRevenue = \App\Models\Payment::where('status', 'completed')
            ->where('paid_at', '>=', now()->subMonth())
            ->sum('amount');
        $prevMonthRevenue = \App\Models\Payment::where('status', 'completed')
            ->whereBetween('paid_at', [now()->subMonths(2), now()->subMonth()])
            ->sum('amount');
        $revenueGrowth = $prevMonthRevenue > 0
            ? round((($lastMonthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100)
            : 0;

        // User stats
        $totalUsers = \App\Models\User::count();
        $newUsersThisMonth = \App\Models\User::where('created_at', '>=', now()->subMonth())->count();
        $userGrowth = $totalUsers > 0 ? round(($newUsersThisMonth / $totalUsers) * 100) : 0;

        // Enrollment stats
        $totalEnrollments = \App\Models\Enrollment::count();
        $newEnrollmentsThisMonth = \App\Models\Enrollment::where('created_at', '>=', now()->subMonth())->count();
        $enrollmentGrowth = $totalEnrollments > 0 ? round(($newEnrollmentsThisMonth / $totalEnrollments) * 100) : 0;

        // Revenue chart (last 12 months)
        $revenueChart = collect(range(11, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            $amount = \App\Models\Payment::where('status', 'completed')
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('amount');
            return [
                'label' => $date->format('M'),
                'amount' => $amount,
            ];
        })->values();

        // Enrollment chart (last 6 months)
        $enrollmentChart = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            $count = \App\Models\Enrollment::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            return [
                'label' => $date->format('M Y'),
                'count' => $count,
            ];
        })->values();

        // Top courses
        $topCourses = \App\Models\Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'enrollments' => $c->enrollments_count,
                'revenue' => $c->enrollments()->sum('amount'),
            ]);

        // Recent enrollments
        $recentEnrollments = \App\Models\Enrollment::with(['user:id,name', 'course:id,title'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'user' => $e->user,
                'course' => $e->course,
                'created_at' => $e->created_at->toISOString(),
            ]);

        return Inertia::render('Admin/Analytics', [
            'stats' => [
                'total_revenue' => $totalRevenue,
                'revenue_growth' => $revenueGrowth,
                'total_users' => $totalUsers,
                'user_growth' => $userGrowth,
                'total_enrollments' => $totalEnrollments,
                'enrollment_growth' => $enrollmentGrowth,
                'completion_rate' => 78, // Placeholder
            ],
            'revenueChart' => $revenueChart,
            'enrollmentChart' => $enrollmentChart,
            'topCourses' => $topCourses,
            'recentEnrollments' => $recentEnrollments,
        ]);
    })->name('admin.analytics');

    // Admin Support Tickets
    Route::get('/tickets', function () {
        $query = \App\Models\SupportTicket::with(['user:id,name,email']);

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $tickets = $query->latest()->paginate(20);

        $stats = [
            'total' => \App\Models\SupportTicket::count(),
            'open' => \App\Models\SupportTicket::where('status', 'open')->count(),
            'pending' => \App\Models\SupportTicket::where('status', 'pending')->count(),
            'resolved' => \App\Models\SupportTicket::where('status', 'resolved')->count(),
        ];

        return Inertia::render('Admin/Tickets/Index', [
            'tickets' => $tickets->through(fn($t) => [
                'id' => $t->id,
                'subject' => $t->subject,
                'message' => \Str::limit($t->message, 100),
                'status' => $t->status,
                'priority' => $t->priority ?? 'normal',
                'user' => $t->user ? ['id' => $t->user->id, 'name' => $t->user->name, 'email' => $t->user->email] : null,
                'created_at' => $t->created_at->toISOString(),
            ]),
            'stats' => $stats,
            'filters' => request()->only(['search', 'status']),
        ]);
    })->name('admin.tickets.index');

    Route::get('/tickets/{id}', function ($id) {
        $ticket = \App\Models\SupportTicket::with(['user:id,name,email', 'replies.user:id,name'])->findOrFail($id);

        return Inertia::render('Admin/Tickets/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'message' => $ticket->message,
                'status' => $ticket->status,
                'priority' => $ticket->priority ?? 'normal',
                'user' => $ticket->user,
                'created_at' => $ticket->created_at->toISOString(),
                'replies' => $ticket->replies ?? [],
            ],
        ]);
    })->name('admin.tickets.show');

    Route::post('/tickets/{id}/reply', function ($id) {
        $ticket = \App\Models\SupportTicket::findOrFail($id);

        $validated = request()->validate([
            'message' => 'required|string|max:2000',
        ]);

        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $ticket->update(['status' => 'pending']);

        return back()->with('success', 'Reply sent.');
    })->name('admin.tickets.reply');

    Route::post('/tickets/{id}/close', function ($id) {
        \App\Models\SupportTicket::where('id', $id)->update(['status' => 'resolved']);
        return back()->with('success', 'Ticket closed.');
    })->name('admin.tickets.close');

    Route::post('/settings/{group}', function ($group) {
        $data = request()->all();

        foreach ($data as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value, 'group' => $group]
            );
        }

        return back()->with('success', 'Settings saved successfully.');
    })->name('admin.settings.update');

    // Review Moderation
    Route::get('/reviews', function () {
        $reviews = \App\Models\Review::with(['student:id,name,email', 'course:id,title', 'teacher:id,name'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'status' => $r->status ?? 'pending',
                'created_at' => $r->created_at->toISOString(),
                'student' => $r->student ? ['name' => $r->student->name, 'email' => $r->student->email] : null,
                'course' => $r->course ? ['title' => $r->course->title] : null,
                'teacher' => $r->teacher ? ['name' => $r->teacher->name] : null,
            ]);

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews,
            'filters' => ['status' => request('status', 'pending')],
        ]);
    })->name('admin.reviews.index');

    Route::post('/reviews/{id}/approve', function ($id) {
        \App\Models\Review::where('id', $id)->update(['status' => 'approved', 'is_visible' => true]);
        return back()->with('success', 'Review approved successfully.');
    })->name('admin.reviews.approve');

    Route::post('/reviews/{id}/reject', function ($id) {
        \App\Models\Review::where('id', $id)->update(['status' => 'rejected', 'is_visible' => false]);
        return back()->with('success', 'Review rejected.');
    })->name('admin.reviews.reject');

    Route::delete('/reviews/{id}', function ($id) {
        \App\Models\Review::where('id', $id)->delete();
        return back()->with('success', 'Review deleted.');
    })->name('admin.reviews.delete');

    // ==========================================
    // ADMIN CERTIFICATE MANAGEMENT
    // ==========================================

    // Certificates Index
    Route::get('/certificates', function () {
        $query = \App\Models\Certificate::with(['user:id,name,email', 'course:id,title']);

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if (request('course_id')) {
            $query->where('course_id', request('course_id'));
        }

        $certificates = $query->latest()->paginate(20);
        $courses = \App\Models\Course::select('id', 'title')->get();

        $stats = [
            'total_certificates' => \App\Models\Certificate::count(),
            'this_month' => \App\Models\Certificate::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'pending_enrollments' => \App\Models\Enrollment::where('status', 'completed')
                ->whereDoesntHave('certificate')->count(),
        ];

        return Inertia::render('Admin/Certificates/Index', [
            'certificates' => $certificates,
            'courses' => $courses,
            'stats' => $stats,
            'filters' => request()->only(['search', 'course_id']),
        ]);
    })->name('admin.certificates.index');

    // Eligible Students for Certificate Generation
    Route::get('/certificates/eligible', function () {
        // Get completed enrollments without certificates
        $eligibleEnrollments = \App\Models\Enrollment::where('status', 'completed')
            ->whereDoesntHave('certificate')
            ->with(['user:id,name,email', 'course:id,title'])
            ->latest()
            ->paginate(20);

        $courses = \App\Models\Course::select('id', 'title')->get();

        return Inertia::render('Admin/Certificates/Eligible', [
            'enrollments' => $eligibleEnrollments,
            'courses' => $courses,
        ]);
    })->name('admin.certificates.eligible');

    // Student Progress for Certificates
    Route::get('/certificates/student-progress', function () {
        // Get all enrollments with progress information
        $enrollments = \App\Models\Enrollment::with(['user:id,name,email', 'course:id,title', 'certificate'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_enrolled' => \App\Models\Enrollment::count(),
            'in_progress' => \App\Models\Enrollment::where('status', 'active')->count(),
            'completed' => \App\Models\Enrollment::where('status', 'completed')->count(),
            'with_certificates' => \App\Models\Certificate::count(),
        ];

        $courses = \App\Models\Course::select('id', 'title')->get();

        return Inertia::render('Admin/Certificates/Simple', [
            'enrollments' => $enrollments->through(fn($e) => [
                'id' => $e->id,
                'user' => $e->user ? ['id' => $e->user->id, 'name' => $e->user->name, 'email' => $e->user->email] : null,
                'course' => $e->course ? ['id' => $e->course->id, 'title' => $e->course->title] : null,
                'status' => $e->status,
                'progress_percentage' => $e->progress_percentage ?? 0,
                'has_certificate' => $e->certificate !== null,
                'certificate_number' => $e->certificate?->certificate_number,
                'created_at' => $e->created_at->toISOString(),
            ]),
            'stats' => $stats,
            'courses' => $courses,
            'filters' => request()->only(['search', 'status', 'course_id']),
        ]);
    })->name('admin.certificates.student-progress');

    // Generate Certificate for Enrollment
    Route::post('/certificates/generate/{enrollment}', function ($enrollmentId) {
        $enrollment = \App\Models\Enrollment::with(['user', 'course'])
            ->where('status', 'completed')
            ->findOrFail($enrollmentId);

        // Check if certificate already exists
        if ($enrollment->certificate) {
            return back()->with('error', 'Certificate already exists for this enrollment.');
        }

        // Generate unique certificate number
        $certificateNumber = 'CERT-' . strtoupper(uniqid()) . '-' . now()->format('Y');
        $verificationCode = \Str::random(12);

        $certificate = \App\Models\Certificate::create([
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'enrollment_id' => $enrollment->id,
            'certificate_number' => $certificateNumber,
            'verification_code' => $verificationCode,
            'student_name' => $enrollment->user->name,
            'course_title' => $enrollment->course->title,
            'completion_percentage' => $enrollment->progress_percentage ?? 100,
            'course_completed_at' => $enrollment->completed_at ?? now(),
            'issued_at' => now(),
            'issued_by' => auth()->user()->name,
        ]);

        return back()->with('success', "Certificate generated: {$certificateNumber}");
    })->name('admin.certificates.generate');

    // Bulk Generate Certificates
    Route::post('/certificates/bulk-generate', function () {
        $validated = request()->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id',
        ]);

        $generated = 0;
        foreach ($validated['enrollment_ids'] as $enrollmentId) {
            $enrollment = \App\Models\Enrollment::with(['user', 'course'])
                ->where('id', $enrollmentId)
                ->where('status', 'completed')
                ->first();

            if ($enrollment && !$enrollment->certificate) {
                $certificateNumber = 'CERT-' . strtoupper(uniqid()) . '-' . now()->format('Y');

                \App\Models\Certificate::create([
                    'user_id' => $enrollment->user_id,
                    'course_id' => $enrollment->course_id,
                    'enrollment_id' => $enrollment->id,
                    'certificate_number' => $certificateNumber,
                    'verification_code' => \Str::random(12),
                    'student_name' => $enrollment->user->name,
                    'course_title' => $enrollment->course->title,
                    'completion_percentage' => $enrollment->progress_percentage ?? 100,
                    'course_completed_at' => $enrollment->completed_at ?? now(),
                    'issued_at' => now(),
                    'issued_by' => auth()->user()->name,
                ]);
                $generated++;
            }
        }

        return back()->with('success', "{$generated} certificates generated successfully!");
    })->name('admin.certificates.bulk-generate');

    // Delete Certificate
    Route::delete('/certificates/{id}', function ($id) {
        \App\Models\Certificate::where('id', $id)->delete();
        return back()->with('success', 'Certificate deleted.');
    })->name('admin.certificates.delete');
});

// Test route for Pusher verification
Route::get('/test-pusher', function () {
    $testMessage = new \App\Models\ChatMessage([
        'id' => 999,
        'conversation_id' => 1,
        'sender_type' => 'system',
        'message' => 'Test message from Pusher at ' . now()->format('H:i:s'),
        'is_read' => false,
        'created_at' => now(),
    ]);

    try {
        broadcast(new \App\Events\MessageSent($testMessage, 'test-session-id'));
        return response()->json([
            'success' => true,
            'message' => 'Event broadcasted successfully!',
            'time' => now()->format('H:i:s'),
            'channel' => 'chat.test-session-id',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

// ==========================================
// CERTIFICATE ROUTES
// ==========================================

// Public certificate verification (no auth needed)
Route::get('/certificates/verify/{code?}', [App\Http\Controllers\CertificateController::class, 'verify'])
    ->name('certificates.verify');
Route::post('/api/certificates/verify', [App\Http\Controllers\CertificateController::class, 'verifyApi']);

// Student certificate routes (login required)
Route::middleware(['auth'])->prefix('student')->group(function () {
    Route::get('/certificates', [App\Http\Controllers\CertificateController::class, 'index'])
        ->name('certificates.index');
    Route::post('/certificates/generate/{enrollment}', [App\Http\Controllers\CertificateController::class, 'generate'])
        ->name('certificates.generate');
    Route::get('/certificates/{id}/download', [App\Http\Controllers\CertificateController::class, 'download'])
        ->name('certificates.download');
});

require __DIR__ . '/auth.php';
