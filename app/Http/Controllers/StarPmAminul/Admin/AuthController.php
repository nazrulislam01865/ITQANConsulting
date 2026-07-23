<?php

namespace App\Http\Controllers\StarPmAminul\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StarPmAminul\Admin\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('starpmaminul')->check()) {
            return redirect()->route('starpmaminul.admin.dashboard');
        }

        return view('starpmaminul.admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->route('starpmaminul.admin.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('starpmaminul')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('starpmaminul.admin.login');
    }
}
