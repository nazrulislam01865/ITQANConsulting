<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterMenuItemRequest;
use App\Models\FooterMenuItem;
use App\Services\Admin\FooterMenuService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FooterMenuController extends Controller
{
    public function index(): View
    {
        return view('admin.menus.footer', [
            'items' => FooterMenuItem::query()->ordered()->get(),
            'routes' => $this->editableRoutes(),
            'groups' => ['pages' => 'Pages', 'services' => 'Services'],
        ]);
    }

    public function store(FooterMenuItemRequest $request, FooterMenuService $service): RedirectResponse
    {
        $service->create($request->validated());

        return back()->with('success', 'Footer menu item added.');
    }

    public function update(FooterMenuItemRequest $request, FooterMenuItem $menuItem, FooterMenuService $service): RedirectResponse
    {
        $service->update($menuItem, $request->validated());

        return back()->with('success', 'Footer menu item updated.');
    }

    public function destroy(FooterMenuItem $menuItem, FooterMenuService $service): RedirectResponse
    {
        $service->delete($menuItem);

        return back()->with('success', 'Footer menu item deleted.');
    }

    /** @return array<int,string> */
    private function editableRoutes(): array
    {
        return ['home', 'services', 'works', 'catalog', 'about', 'contact'];
    }
}
