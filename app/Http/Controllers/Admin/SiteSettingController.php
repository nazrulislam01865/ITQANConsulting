<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSettingsRequest;
use App\Models\SiteSetting;
use App\Services\Admin\SiteSettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.site-settings.edit', [
            'settings' => SiteSetting::current(),
            'routes' => $this->editableRoutes(),
        ]);
    }

    public function update(SiteSettingsRequest $request, SiteSettingsService $service): RedirectResponse
    {
        $data = $request->safe()->except('logo', 'favicon');
        $service->update($data, $request->file('logo'), $request->file('favicon'));

        return back()->with('success', 'Site settings updated successfully.');
    }

    /** @return array<int,string> */
    private function editableRoutes(): array
    {
        return ['home', 'services', 'works', 'catalog', 'about', 'contact'];
    }
}
