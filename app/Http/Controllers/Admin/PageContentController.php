<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageSectionRequest;
use App\Models\PageSection;
use App\Services\Admin\PageAdminService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PageContentController extends Controller
{
    /** @return array<string,string> */
    private function pages(): array
    {
        return [
            'about' => 'About Page',
            'services' => 'Services Page',
            'works' => 'Works Page',
            'catalog' => 'Catalog Page',
            'contact' => 'Contact Page',
        ];
    }

    public function index(string $pageKey): View
    {
        abort_unless(array_key_exists($pageKey, $this->pages()), 404);

        return view('admin.pages.index', [
            'pageKey' => $pageKey,
            'pageTitle' => $this->pages()[$pageKey],
            'sections' => PageSection::query()->forPage($pageKey)->withCount('items')->ordered()->get(),
        ]);
    }

    public function edit(string $pageKey, PageSection $section): View
    {
        abort_unless(array_key_exists($pageKey, $this->pages()) && $section->page_key === $pageKey, 404);

        $section->load('items');

        return view('admin.pages.edit-section', [
            'pageKey' => $pageKey,
            'pageTitle' => $this->pages()[$pageKey],
            'section' => $section,
            'routes' => $this->editableRoutes(),
        ]);
    }

    public function update(PageSectionRequest $request, PageSection $section, PageAdminService $service): RedirectResponse
    {
        $service->updateSection(
            $section,
            $request->validated(),
            $request->file('qr_image')
        );

        return back()->with('success', $section->admin_title . ' updated successfully.');
    }

    /** @return array<int,string> */
    private function editableRoutes(): array
    {
        return ['home', 'services', 'works', 'catalog', 'about', 'contact'];
    }
}
