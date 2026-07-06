<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SocialLinkRequest;
use App\Models\SocialLink;
use App\Services\Admin\SocialLinkService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SocialLinkController extends Controller
{
    public function index(): View
    {
        return view('admin.social-links.index', [
            'items' => SocialLink::query()->ordered()->get(),
        ]);
    }

    public function store(SocialLinkRequest $request, SocialLinkService $service): RedirectResponse
    {
        $service->create($request->validated());

        return back()->with('success', 'Social link added.');
    }

    public function update(SocialLinkRequest $request, SocialLink $socialLink, SocialLinkService $service): RedirectResponse
    {
        $service->update($socialLink, $request->validated());

        return back()->with('success', 'Social link updated.');
    }

    public function destroy(SocialLink $socialLink, SocialLinkService $service): RedirectResponse
    {
        $service->delete($socialLink);

        return back()->with('success', 'Social link deleted.');
    }
}
