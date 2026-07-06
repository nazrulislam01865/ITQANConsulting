<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageSectionItemRequest;
use App\Models\PageSection;
use App\Models\PageSectionItem;
use App\Services\Admin\PageAdminService;
use Illuminate\Http\RedirectResponse;

class PageSectionItemController extends Controller
{
    public function store(PageSectionItemRequest $request, PageSection $section, PageAdminService $service): RedirectResponse
    {
        $service->createItem($section, $request->validated(), $this->uploadedFiles($request));

        return back()->with('success', 'Section item added successfully.');
    }

    public function update(PageSectionItemRequest $request, PageSectionItem $item, PageAdminService $service): RedirectResponse
    {
        $service->updateItem($item, $request->validated(), $this->uploadedFiles($request));

        return back()->with('success', 'Section item updated successfully.');
    }

    public function destroy(PageSectionItem $item, PageAdminService $service): RedirectResponse
    {
        $service->deleteItem($item);

        return back()->with('success', 'Section item deleted successfully.');
    }

    /** @return array<string,\Illuminate\Http\UploadedFile|null> */
    private function uploadedFiles(PageSectionItemRequest $request): array
    {
        return [
            'image' => $request->file('image'),
            'media_image' => $request->file('media_image'),
            'media_video' => $request->file('media_video'),
            'thumbnail' => $request->file('thumbnail'),
        ];
    }
}
