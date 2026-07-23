<?php

namespace App\Http\Controllers\StarPmAminul;

use App\Http\Controllers\Controller;
use App\Services\StarPmAminul\PortfolioContentService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function show(string $path): StreamedResponse
    {
        abort_if(str_contains($path, '..'), 404);

        $storagePath = 'starpmaminul/'.ltrim($path, '/');
        abort_unless(Storage::disk('public')->exists($storagePath), 404);

        return Storage::disk('public')->response($storagePath, basename($storagePath), [
            'Cache-Control' => 'public, max-age=604800',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function downloadResume(PortfolioContentService $content): StreamedResponse
    {
        $resumePath = trim((string) data_get($content->section('hero'), 'resume_file'));

        abort_if($resumePath === '' || str_contains($resumePath, '..'), 404);
        abort_unless(str_starts_with($resumePath, 'starpmaminul/'), 404);
        abort_unless(Storage::disk('public')->exists($resumePath), 404);

        $extension = strtolower(pathinfo($resumePath, PATHINFO_EXTENSION)) ?: 'pdf';
        $downloadName = 'Md-Aminul-Islam-Resume.'.$extension;

        return Storage::disk('public')->download($resumePath, $downloadName, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
