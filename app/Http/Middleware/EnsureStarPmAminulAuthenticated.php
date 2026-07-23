<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStarPmAminulAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('starpmaminul')->check()) {
            return redirect()
                ->route('starpmaminul.admin.login')
                ->with('status', 'Please sign in to manage the Md Aminul Islam portfolio.');
        }

        return $next($request);
    }
}
