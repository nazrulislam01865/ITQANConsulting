<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\ItqanFrontendContentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function __construct(private readonly ItqanFrontendContentService $contentService)
    {
    }

    public function home(): View
    {
        return $this->render('home');
    }

    public function about(): View
    {
        return $this->render('about');
    }

    public function services(): View
    {
        return $this->render('services');
    }

    public function works(): View
    {
        return $this->render('works');
    }

    public function catalog(): View
    {
        return $this->render('catalog');
    }

    public function contact(): View
    {
        return $this->render('contact');
    }

    public function downloadCatalog()
    {
        $content = $this->contentService->content();

        $data = [
            'site' => $content['site'],
            'catalog' => $content['pages']['catalog'],
            'catalogPages' => $content['collections']['catalog_pages'] ?? [],
        ];

        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return response()->view('frontend.pages.catalog-pdf-unavailable', $data, 501);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('frontend.pages.catalog-pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('itqan-digital-catalog.pdf');
    }

    private function render(string $pageKey): View
    {
        $content = $this->contentService->content();

        abort_unless(isset($content['pages'][$pageKey]), 404);

        return view("frontend.pages.{$pageKey}", [
            'site' => $content['site'],
            'navigation' => $content['navigation'],
            'socialLinks' => $content['social_links'],
            'footer' => $content['footer'],
            'page' => $content['pages'][$pageKey],
            'collections' => $content['collections'],
        ]);
    }
}
