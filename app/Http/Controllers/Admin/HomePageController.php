<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSectionRequest;
use App\Models\HomeSection;
use App\Services\Admin\HomePageAdminService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HomePageController extends Controller
{
    public function index(): View
    {
        return view('admin.home.index', [
            'sections' => HomeSection::query()->withCount('items')->ordered()->get(),
        ]);
    }

    public function edit(HomeSection $section): View
    {
        $section->load('items');

        return view('admin.home.edit-section', [
            'section' => $section,
            'routes' => $this->editableRoutes(),
        ]);
    }

    public function update(HomeSectionRequest $request, HomeSection $section, HomePageAdminService $service): RedirectResponse
    {
        $service->updateSection(
            $section,
            $request->validated(),
            $request->file('founder_image'),
            $request->file('qr_image')
        );

        return back()->with('success', 'Home section updated successfully.');
    }

    /** @return array<int,string> */
    private function editableRoutes(): array
    {
        return ['home', 'services', 'works', 'catalog', 'about', 'contact'];
    }
}
