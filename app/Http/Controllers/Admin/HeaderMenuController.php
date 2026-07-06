<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemRequest;
use App\Models\NavigationMenuItem;
use App\Services\Admin\NavigationMenuService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HeaderMenuController extends Controller
{
    public function index(): View
    {
        return view('admin.menus.header', [
            'items' => NavigationMenuItem::query()->ordered()->get(),
            'routes' => $this->editableRoutes(),
        ]);
    }

    public function store(MenuItemRequest $request, NavigationMenuService $service): RedirectResponse
    {
        $service->create($request->validated());

        return back()->with('success', 'Header menu item added.');
    }

    public function update(MenuItemRequest $request, NavigationMenuItem $menuItem, NavigationMenuService $service): RedirectResponse
    {
        $service->update($menuItem, $request->validated());

        return back()->with('success', 'Header menu item updated.');
    }

    public function destroy(NavigationMenuItem $menuItem, NavigationMenuService $service): RedirectResponse
    {
        $service->delete($menuItem);

        return back()->with('success', 'Header menu item deleted.');
    }

    /** @return array<int,string> */
    private function editableRoutes(): array
    {
        return ['home', 'services', 'works', 'catalog', 'about', 'contact'];
    }
}
