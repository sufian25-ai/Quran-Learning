<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                ]);

                // Assign default role (student)
                $user->assignRole('student');
            } else {
                // Update google_id if not present
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'id_active' => true,
                    ]);

                    if (!$user->avatar) {
                        $user->update(['avatar' => $googleUser->getAvatar()]);
                    }
                }
            }

            Auth::login($user);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage());
            return redirect()->route('login')->with('message', 'Google authentication failed. Please try again.');
        }
    }
}
