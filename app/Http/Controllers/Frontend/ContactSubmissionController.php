<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactInquiryReceived;
use App\Models\ContactSubmission;
use App\Services\Frontend\RecaptchaVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactSubmissionController extends Controller
{
    public function store(Request $request, RecaptchaVerifier $captchaVerifier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'company' => ['nullable', 'string', 'max:180'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'need' => ['nullable', 'string', 'max:180'],
            'areas' => ['nullable', 'array', 'max:20'],
            'areas.*' => ['string', 'max:180'],
            'support_types' => ['nullable', 'array', 'max:20'],
            'support_types.*' => ['string', 'max:180'],
            'budget' => ['nullable', 'string', 'max:180'],
            'method' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
            'g-recaptcha-response' => ['nullable', 'string', 'max:4096'],
        ]);

        if (! $captchaVerifier->verify($request->input('g-recaptcha-response'), $request->ip())) {
            return back()
                ->withInput($request->except('g-recaptcha-response'))
                ->withErrors(['captcha' => 'Please complete the CAPTCHA verification and try again.']);
        }

        $submission = ContactSubmission::query()->create([
            'name' => $validated['name'],
            'company_name' => $validated['company'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'need' => $validated['need'] ?? ($validated['support_types'][0] ?? null),
            'areas' => array_values($validated['areas'] ?? []),
            'support_types' => array_values($validated['support_types'] ?? []),
            'budget_range' => $validated['budget'] ?? null,
            'preferred_contact_method' => $validated['method'] ?? null,
            'message' => $validated['message'],
            'status' => 'unread',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        $recipient = trim((string) config('itqan_contact.recipient_email', 'aminul@itqanconsulting.com'));

        if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($recipient)->send(new ContactInquiryReceived($submission));
            } catch (Throwable $exception) {
                Log::error('Contact inquiry was saved but the notification email could not be sent.', [
                    'submission_id' => $submission->id,
                    'recipient' => $recipient,
                    'message' => $exception->getMessage(),
                ]);
            }
        } else {
            Log::warning('Contact inquiry notification recipient is not a valid email address.', [
                'submission_id' => $submission->id,
                'recipient' => $recipient,
            ]);
        }

        return back()->with('status', 'Thank you. Your message has been received. ITQAN will contact you soon.');
    }
}
