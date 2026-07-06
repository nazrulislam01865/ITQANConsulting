<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use App\Services\Admin\AdminAuthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function __construct(private readonly AdminAuthService $adminAuthService)
    {
    }

    public function show(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()?->canAccessAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function store(AdminLoginRequest $request): RedirectResponse
    {
        $this->adminAuthService->login($request->validated());

        return redirect()->intended(route('admin.dashboard'));
    }


    public function expired(): RedirectResponse
    {
        $this->adminAuthService->logout();

        return redirect()->route('admin.login')->with('status', 'Your admin session expired. Please sign in again.');
    }

    public function destroy(): RedirectResponse
    {
        $this->adminAuthService->logout();

        return redirect()->route('admin.login')->with('status', 'You have been signed out successfully.');
    }
}
