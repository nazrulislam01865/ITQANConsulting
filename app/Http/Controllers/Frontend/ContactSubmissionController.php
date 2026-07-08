<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'company' => ['nullable', 'string', 'max:180'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'need' => ['nullable', 'string', 'max:180'],
            'budget' => ['nullable', 'string', 'max:180'],
            'method' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactSubmission::query()->create([
            'name' => $validated['name'],
            'company_name' => $validated['company'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'need' => $validated['need'] ?? null,
            'areas' => [],
            'budget_range' => $validated['budget'] ?? null,
            'preferred_contact_method' => $validated['method'] ?? null,
            'message' => $validated['message'],
            'status' => 'unread',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return back()->with('status', 'Thank you. Your message has been received. ITQAN will contact you soon.');
    }
}
