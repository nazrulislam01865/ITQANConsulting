<?php

namespace App\Http\Controllers\StarPmAminul;

use App\Http\Controllers\Controller;
use App\Services\StarPmAminul\PortfolioContentService;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(PortfolioContentService $content): View
    {
        return view('starpmaminul.portfolio.index', $content->frontendPayload());
    }
}
