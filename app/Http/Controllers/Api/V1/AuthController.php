<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LearningStreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     * 
     * @group Authentication
     * @unauthenticated
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'timezone' => ['nullable', 'string', 'max:50'],
            'country_code' => ['nullable', 'string', 'max:3'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'timezone' => $validated['timezone'] ?? 'UTC',
            'country_code' => $validated['country_code'] ?? null,
        ]);

        // Assign student role by default
        $user->assignRole('student');

        // Create learning streak record
        LearningStreak::create([
            'user_id' => $user->id,
            'current_streak' => 0,
            'longest_streak' => 0,
        ]);

        // Send email verification
        $user->sendEmailVerificationNotification();

        // Create API token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. Please verify your email.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar_url' => $user->avatar_url,
                    'roles' => $user->getRoleNames(),
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login user
     * 
     * @group Authentication
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Create token
        $deviceName = $validated['device_name'] ?? 'default';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar_url' => $user->avatar_url,
                    'timezone' => $user->timezone,
                    'language' => $user->language,
                    'is_email_verified' => $user->hasVerifiedEmail(),
                    'roles' => $user->getRoleNames(),
                    'points' => $user->points,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout user (revoke current token)
     * 
     * @group Authentication
     * @authenticated
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Logout from all devices
     * 
     * @group Authentication
     * @authenticated
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices',
        ]);
    }

    /**
     * Get authenticated user profile
     * 
     * @group Authentication
     * @authenticated
     */
    public function user(Request $request)
    {
        $user = $request->user()->load(['teacherProfile', 'learningStreak', 'badges']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar_url' => $user->avatar_url,
                'timezone' => $user->timezone,
                'language' => $user->language,
                'country_code' => $user->country_code,
                'is_email_verified' => $user->hasVerifiedEmail(),
                'is_active' => $user->is_active,
                'points' => $user->points,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'teacher_profile' => $user->teacherProfile,
                'learning_streak' => $user->learningStreak,
                'badges' => $user->badges,
                'created_at' => $user->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Update user profile
     * 
     * @group Authentication
     * @authenticated
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:10'],
            'country_code' => ['nullable', 'string', 'max:3'],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'timezone' => $user->timezone,
                'language' => $user->language,
                'country_code' => $user->country_code,
            ],
        ]);
    }

    /**
     * Change password
     * 
     * @group Authentication
     * @authenticated
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Revoke all tokens except current
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Request password reset
     * 
     * @group Authentication
     * @unauthenticated
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // In production, send password reset email
            // Password::sendResetLink($request->only('email'));
        }

        // Always return success to prevent email enumeration
        return response()->json([
            'success' => true,
            'message' => 'If an account exists with that email, a password reset link has been sent.',
        ]);
    }

    /**
     * Resend email verification
     * 
     * @group Authentication
     * @authenticated
     */
    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified.',
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent.',
        ]);
    }
}
