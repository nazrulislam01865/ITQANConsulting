<?php

namespace App\Support;

use Illuminate\Support\Str;

final class StarPmAminulMedia
{
    public static function url(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return '';
        }

        $path = Str::after($path, 'starpmaminul/');

        return route('starpmaminul.media', ['path' => ltrim($path, '/')]);
    }
}
