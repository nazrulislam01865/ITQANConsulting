<?php

namespace App\Http\Controllers\StarPmAminul;

use App\Http\Controllers\Controller;
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
}
