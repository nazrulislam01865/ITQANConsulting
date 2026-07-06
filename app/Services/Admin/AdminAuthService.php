<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthService
{
    /**
     * @param  array{email:string,password:string,remember?:bool}  $credentials
     */
    public function login(array $credentials): void
    {
        $remember = (bool) ($credentials['remember'] ?? false);

        $attempt = Auth::attempt([
            'email' => mb_strtolower($credentials['email']),
            'password' => $credentials['password'],
            'is_admin' => true,
            'is_active' => true,
        ], $remember);

        if (! $attempt) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match an active administrator account.',
            ]);
        }

        request()->session()->regenerate();
        request()->session()->put('admin_last_activity_at', now()->timestamp);
        Auth::user()?->forceFill(['last_login_at' => now()])->save();
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
