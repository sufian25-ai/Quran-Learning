<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseDetailResource;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * List all published courses
     * 
     * @group Courses
     * @unauthenticated
     */
    public function index(Request $request)
    {
        $query = Course::published()
            ->with([
                'batches' => function ($q) {
                    $q->where('status', 'upcoming')
                        ->orWhere('status', 'active');
                }
            ]);

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'popularity');
        switch ($sortBy) {
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
            default: // popularity
                $query->orderBy('popularity_score', 'desc');
        }

        $courses = $query->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => CourseResource::collection($courses),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ]);
    }

    /**
     * Get featured courses
     * 
     * @group Courses
     * @unauthenticated
     */
    public function featured()
    {
        $courses = Course::published()
            ->featured()
            ->orderBy('popularity_score', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => CourseResource::collection($courses),
        ]);
    }

    /**
     * Get course by slug
     * 
     * @group Courses
     * @unauthenticated
     */
    public function show(string $slug)
    {
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
                'publicResources',
            ])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new CourseDetailResource($course),
        ]);
    }

    /**
     * Get available categories
     * 
     * @group Courses
     * @unauthenticated
     */
    public function categories()
    {
        $categories = [
            ['id' => 'quran_reading', 'name' => 'Quran Reading', 'icon' => 'book-open'],
            ['id' => 'tajweed', 'name' => 'Tajweed', 'icon' => 'music'],
            ['id' => 'hifz', 'name' => 'Hifz (Memorization)', 'icon' => 'brain'],
            ['id' => 'arabic', 'name' => 'Arabic Language', 'icon' => 'language'],
            ['id' => 'islamic_studies', 'name' => 'Islamic Studies', 'icon' => 'mosque'],
        ];

        // Add course counts
        foreach ($categories as &$category) {
            $category['course_count'] = Course::published()
                ->where('category', $category['id'])
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get available levels
     * 
     * @group Courses
     * @unauthenticated
     */
    public function levels()
    {
        return response()->json([
            'success' => true,
            'data' => [
                ['id' => 'beginner', 'name' => 'Beginner', 'description' => 'No prior knowledge required'],
                ['id' => 'intermediate', 'name' => 'Intermediate', 'description' => 'Some foundational knowledge'],
                ['id' => 'advanced', 'name' => 'Advanced', 'description' => 'Strong foundation required'],
                ['id' => 'all_levels', 'name' => 'All Levels', 'description' => 'Suitable for everyone'],
            ],
        ]);
    }
}
