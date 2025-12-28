<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request)
    {
        $query = Course::withCount(['batches', 'enrollments']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $courses->items(),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ]);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'syllabus' => ['nullable', 'string'],
            'level' => ['required', 'in:beginner,intermediate,advanced,all_levels'],
            'category' => ['required', 'string'],
            'duration_weeks' => ['required', 'integer', 'min:1'],
            'classes_per_week' => ['required', 'integer', 'min:1'],
            'class_duration_minutes' => ['required', 'integer', 'min:15'],
            'price_group' => ['required', 'numeric', 'min:0'],
            'price_private' => ['required', 'numeric', 'min:0'],
            'max_students_per_batch' => ['required', 'integer', 'min:1'],
            'languages' => ['nullable', 'array'],
            'requirements' => ['nullable', 'array'],
            'learning_outcomes' => ['nullable', 'array'],
            'is_featured' => ['boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Ensure unique slug
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Course::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        $course = Course::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => $course,
        ], 201);
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load(['batches.teacher', 'reviews', 'resources']);
        $course->loadCount(['batches', 'enrollments', 'reviews']);

        return response()->json([
            'success' => true,
            'data' => $course,
        ]);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'syllabus' => ['nullable', 'string'],
            'level' => ['sometimes', 'in:beginner,intermediate,advanced,all_levels'],
            'category' => ['sometimes', 'string'],
            'duration_weeks' => ['sometimes', 'integer', 'min:1'],
            'classes_per_week' => ['sometimes', 'integer', 'min:1'],
            'class_duration_minutes' => ['sometimes', 'integer', 'min:15'],
            'price_group' => ['sometimes', 'numeric', 'min:0'],
            'price_private' => ['sometimes', 'numeric', 'min:0'],
            'max_students_per_batch' => ['sometimes', 'integer', 'min:1'],
            'languages' => ['nullable', 'array'],
            'requirements' => ['nullable', 'array'],
            'learning_outcomes' => ['nullable', 'array'],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
        ]);

        // If publishing, set published_at
        if (!empty($validated['is_published']) && !$course->published_at) {
            $validated['published_at'] = now();
        }

        $course->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => $course->fresh(),
        ]);
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        // Check if course has active enrollments
        if ($course->enrollments()->active()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete course with active enrollments.',
            ], 400);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully',
        ]);
    }

    /**
     * Publish a course.
     */
    public function publish(Course $course)
    {
        $course->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course published successfully',
        ]);
    }

    /**
     * Unpublish a course.
     */
    public function unpublish(Course $course)
    {
        $course->update(['is_published' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Course unpublished successfully',
        ]);
    }
}
