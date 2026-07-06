<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'mark_text',
        'logo_path',
        'tagline',
        'email',
        'address',
        'description',
        'primary_cta_text',
        'primary_cta_route',
        'footer_bottom_left',
        'copyright',
    ];

    public static function current(): ?self
    {
        return self::query()->first();
    }

    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        $path = ltrim((string) $this->logo_path, '/');

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            $storagePath = Str::after($path, 'storage/');
            return Storage::disk('public')->exists($storagePath) ? asset($path) : null;
        }

        if (Str::startsWith($path, 'uploads/')) {
            return file_exists(public_path($path)) ? asset($path) : null;
        }

        return Storage::disk('public')->exists($path) ? asset('storage/'.$path) : null;
    }
}
