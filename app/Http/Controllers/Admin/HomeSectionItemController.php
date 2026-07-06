<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSectionItemRequest;
use App\Models\HomeSection;
use App\Models\HomeSectionItem;
use App\Services\Admin\HomePageAdminService;
use Illuminate\Http\RedirectResponse;

class HomeSectionItemController extends Controller
{
    public function store(HomeSectionItemRequest $request, HomeSection $section, HomePageAdminService $service): RedirectResponse
    {
        $service->createItem($section, $request->validated(), $request->file('image'));

        return back()->with('success', 'Section item added successfully.');
    }

    public function update(HomeSectionItemRequest $request, HomeSectionItem $item, HomePageAdminService $service): RedirectResponse
    {
        $service->updateItem($item, $request->validated(), $request->file('image'));

        return back()->with('success', 'Section item updated successfully.');
    }

    public function destroy(HomeSectionItem $item, HomePageAdminService $service): RedirectResponse
    {
        $service->deleteItem($item);

        return back()->with('success', 'Section item deleted successfully.');
    }
}
