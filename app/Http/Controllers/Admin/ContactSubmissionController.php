<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

class ContactSubmissionController extends Controller
{
    public function index(): View
    {
        abort_unless(Schema::hasTable('contact_submissions'), 500, 'Please run migrations first.');

        return view('admin.contact-submissions.index', [
            'items' => ContactSubmission::query()->latestFirst()->paginate(20),
            'totalCount' => ContactSubmission::query()->count(),
            'unreadCount' => ContactSubmission::query()->where('status', 'unread')->count(),
        ]);
    }

    public function show(ContactSubmission $contactSubmission): View
    {
        if ($contactSubmission->isUnread()) {
            $contactSubmission->forceFill(['status' => 'read'])->save();
        }

        return view('admin.contact-submissions.show', [
            'submission' => $contactSubmission,
        ]);
    }

    public function destroy(ContactSubmission $contactSubmission): RedirectResponse
    {
        $contactSubmission->delete();

        return redirect()
            ->route('admin.contact-submissions.index')
            ->with('status', 'Contact response deleted.');
    }
}
