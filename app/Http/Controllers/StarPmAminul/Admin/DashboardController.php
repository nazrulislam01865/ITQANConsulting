<?php

namespace App\Http\Controllers\StarPmAminul\Admin;

use App\Http\Controllers\Controller;
use App\Services\StarPmAminul\PortfolioContentService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(PortfolioContentService $content): View
    {
        return view('starpmaminul.admin.dashboard', [
            'sections' => config('starpmaminul.sections', []),
            'content' => $content->all(),
        ]);
    }
}
