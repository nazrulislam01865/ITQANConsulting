<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterMenuItem;
use App\Models\HomeSection;
use App\Models\NavigationMenuItem;
use App\Models\PageSection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard.index', [
            'homeSections' => HomeSection::query()->count(),
            'pageSections' => Schema::hasTable('page_sections') ? PageSection::query()->count() : 0,
            'headerMenuItems' => NavigationMenuItem::query()->count(),
            'footerMenuItems' => FooterMenuItem::query()->count(),
        ]);
    }
}
