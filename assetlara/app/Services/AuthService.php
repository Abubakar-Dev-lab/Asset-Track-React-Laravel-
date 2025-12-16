<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Handle the authentication attempt.
     * Throws ValidationException on failure to show errors on the form.
     */
    public function authenticate(array $credentials, bool $remember = false): void
    {
        // 1. Attempt to login (Hashes password & checks DB)
        if (! Auth::attempt($credentials, $remember)) {
            // SECURITY NOTE: We use a generic message to prevent User Enumeration
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // 2. Business Rule: Check if the user is "Active"
        // We do this AFTER password check so we know they are who they say they are.
        if (! Auth::user()->is_active) {
            Auth::logout(); // Log them out immediately

            throw ValidationException::withMessages([
                'email' => 'Your account is deactivated. Contact Admin.',
            ]);
        }

        // 3. Security: Regenerate Session ID
        // This prevents "Session Fixation" attacks (Hijacking a cookie)
        request()->session()->regenerate();
    }

    /**
     * Handle the logout process.
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Create a new user and return the instance.
     *
     * @param array $data Validated user data (name, email, password)
     * @return User The newly created and authenticated user
     */
    public function register(array $data): User
    {
        // 1. Create the user with a hashed password and default role
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'employee', // New registrations are employees by default
            'is_active' => true,
        ]);

        return $user;
    }
}
