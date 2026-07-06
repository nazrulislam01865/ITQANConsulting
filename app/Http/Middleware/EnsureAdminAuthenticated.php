<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! Auth::user()?->canAccessAdmin()) {
            Auth::logout();

            return redirect()->route('admin.login')->with('status', 'Please sign in with an active administrator account.');
        }

        $timeoutSeconds = max(1, (int) config('itqan_security.admin_session_timeout_minutes', 30)) * 60;
        $lastActivity = (int) $request->session()->get('admin_last_activity_at', 0);
        $now = now()->timestamp;

        if ($lastActivity > 0 && ($now - $lastActivity) > $timeoutSeconds) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->with('status', 'Your admin session expired. Please sign in again.');
        }

        $request->session()->put('admin_last_activity_at', $now);

        return $next($request);
    }
}
