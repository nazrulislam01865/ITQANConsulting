<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\FooterMenuItem;
use App\Models\HomeSection;
use App\Models\NavigationMenuItem;
use App\Models\PageSection;
use App\Models\ExternalGuestMap\Place as GuestMapPlace;
use App\Models\ExternalGuestMap\Edge as GuestMapEdge;
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
            'contactResponses' => Schema::hasTable('contact_submissions') ? ContactSubmission::query()->count() : 0,
            'unreadContactResponses' => Schema::hasTable('contact_submissions') ? ContactSubmission::query()->where('status', 'unread')->count() : 0,
            'guestMapPlaces' => Schema::hasTable('ext_guest_map_places') ? GuestMapPlace::query()->count() : 0,
            'guestMapPaths' => Schema::hasTable('ext_guest_map_edges') ? GuestMapEdge::query()->count() : 0,
        ]);
    }
}
