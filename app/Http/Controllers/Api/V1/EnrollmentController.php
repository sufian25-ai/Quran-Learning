<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Batch;
use App\Http\Resources\EnrollmentResource;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * List user's enrollments
     * 
     * @group Enrollments
     * @authenticated
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = $user->enrollments()
            ->with(['course:id,title,slug,thumbnail,level,category', 'batch:id,name,start_date,schedule']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $enrollments = $query->latest()->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => EnrollmentResource::collection($enrollments),
            'meta' => [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'per_page' => $enrollments->perPage(),
                'total' => $enrollments->total(),
            ],
        ]);
    }

    /**
     * Get active enrollments
     * 
     * @group Enrollments
     * @authenticated
     */
    public function active(Request $request)
    {
        $enrollments = $request->user()
            ->enrollments()
            ->active()
            ->with(['course:id,title,slug,thumbnail', 'batch:id,name,schedule'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => EnrollmentResource::collection($enrollments),
        ]);
    }

    /**
     * Get single enrollment details
     * 
     * @group Enrollments
     * @authenticated
     */
    public function show(Request $request, Enrollment $enrollment)
    {
        // Ensure user owns this enrollment
        if ($enrollment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found',
            ], 404);
        }

        $enrollment->load([
            'course',
            'batch.teacher:id,name,avatar',
            'payments',
            'progress',
            'certificate',
        ]);

        return response()->json([
            'success' => true,
            'data' => new EnrollmentResource($enrollment),
        ]);
    }

    /**
     * Preview enrollment (before payment)
     * 
     * @group Enrollments
     * @authenticated
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'type' => ['required', 'in:group,private'],
            'batch_id' => ['required_if:type,group', 'exists:batches,id'],
            'billing_cycle' => ['nullable', 'in:monthly,quarterly,yearly'],
            'coupon_code' => ['nullable', 'string'],
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $billingCycle = $validated['billing_cycle'] ?? 'monthly';

        // Calculate base price
        $basePrice = $validated['type'] === 'group'
            ? $course->price_group
            : $course->price_private;

        // Apply billing cycle multiplier
        $multiplier = match ($billingCycle) {
            'quarterly' => 3 * 0.9, // 10% discount
            'yearly' => 12 * 0.8, // 20% discount
            default => 1,
        };

        $amount = $basePrice * $multiplier;
        $discount = 0;

        // Apply coupon if provided
        if (!empty($validated['coupon_code'])) {
            $coupon = \App\Models\Coupon::byCode($validated['coupon_code'])
                ->active()
                ->first();

            if ($coupon && $coupon->canBeUsedByUser($request->user())) {
                $discount = $coupon->calculateDiscount($amount);
            }
        }

        $finalAmount = $amount - $discount;

        // Get batch details if group
        $batch = null;
        if ($validated['type'] === 'group' && !empty($validated['batch_id'])) {
            $batch = Batch::with('teacher:id,name,avatar')
                ->find($validated['batch_id']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'thumbnail' => $course->thumbnail_url,
                ],
                'type' => $validated['type'],
                'batch' => $batch ? [
                    'id' => $batch->id,
                    'name' => $batch->name,
                    'start_date' => $batch->start_date->toDateString(),
                    'schedule' => $batch->formatted_schedule,
                    'teacher' => $batch->teacher,
                    'available_slots' => $batch->available_slots,
                ] : null,
                'billing_cycle' => $billingCycle,
                'pricing' => [
                    'base_price' => round($basePrice, 2),
                    'subtotal' => round($amount, 2),
                    'discount' => round($discount, 2),
                    'total' => round($finalAmount, 2),
                    'currency' => 'USD',
                ],
            ],
        ]);
    }

    /**
     * Create enrollment (after payment)
     * 
     * @group Enrollments
     * @authenticated
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'type' => ['required', 'in:group,private'],
            'batch_id' => ['required_if:type,group', 'exists:batches,id'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,yearly,one_time'],
            'payment_method_id' => ['required', 'string'], // Stripe payment method
            'coupon_code' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $course = Course::findOrFail($validated['course_id']);

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'pending'])
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course.',
            ], 400);
        }

        // For group enrollment, check batch availability
        $batch = null;
        if ($validated['type'] === 'group') {
            $batch = Batch::findOrFail($validated['batch_id']);

            if (!$batch->hasAvailableSlots()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This batch is full. Please select another batch.',
                ], 400);
            }
        }

        // Calculate amount
        $basePrice = $validated['type'] === 'group' ? $course->price_group : $course->price_private;
        $multiplier = match ($validated['billing_cycle']) {
            'quarterly' => 3 * 0.9,
            'yearly' => 12 * 0.8,
            default => 1,
        };
        $amount = $basePrice * $multiplier;
        $discount = 0;

        // Apply coupon
        if (!empty($validated['coupon_code'])) {
            $coupon = \App\Models\Coupon::byCode($validated['coupon_code'])->active()->first();
            if ($coupon && $coupon->canBeUsedByUser($user)) {
                $discount = $coupon->calculateDiscount($amount);
            }
        }

        $finalAmount = $amount - $discount;

        // Process payment with Stripe
        try {
            $paymentService = app(\App\Services\PaymentService::class);

            // Create enrollment first (pending)
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'batch_id' => $batch?->id,
                'type' => $validated['type'],
                'status' => 'pending',
                'amount' => $finalAmount,
                'currency' => 'USD',
                'billing_cycle' => $validated['billing_cycle'],
                'start_date' => $batch?->start_date ?? now(),
                'coupon_code' => $validated['coupon_code'] ?? null,
                'discount_amount' => $discount,
                'classes_total' => $course->duration_weeks * $course->classes_per_week,
            ]);

            // Process payment
            $paymentResult = $paymentService->processStripe($enrollment, $validated['payment_method_id']);

            if (!$paymentResult['success']) {
                $enrollment->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed: ' . ($paymentResult['error'] ?? 'Unknown error'),
                ], 400);
            }

            // Activate enrollment
            $enrollment->update(['status' => 'active']);

            // Update batch enrollment count
            if ($batch) {
                $batch->incrementEnrollment();
            }

            // Update course stats
            $course->incrementEnrollments();

            // Record coupon usage
            if (!empty($validated['coupon_code']) && isset($coupon)) {
                $coupon->recordUsage($user, $enrollment, $discount);
            }

            $enrollment->load(['course', 'batch.teacher']);

            return response()->json([
                'success' => true,
                'message' => 'Enrollment successful! Welcome to the course.',
                'data' => new EnrollmentResource($enrollment),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during enrollment. Please try again.',
            ], 500);
        }
    }

    /**
     * Cancel enrollment
     * 
     * @group Enrollments
     * @authenticated
     */
    public function cancel(Request $request, Enrollment $enrollment)
    {
        if ($enrollment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found',
            ], 404);
        }

        if ($enrollment->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Only active enrollments can be cancelled.',
            ], 400);
        }

        // Check if within refund period (7 days)
        $canRefund = $enrollment->created_at->diffInDays(now()) <= 7;

        $enrollment->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment cancelled.' . ($canRefund ? ' Refund will be processed within 5-7 business days.' : ''),
            'data' => [
                'refund_eligible' => $canRefund,
            ],
        ]);
    }
}
