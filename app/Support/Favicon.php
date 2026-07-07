<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class Favicon
{
    /** @var array<int,string> */
    public const FILES = [
        'favicon.ico',
        'favicon.png',
        'favicon.webp',
        'favicon.jpg',
        'favicon.jpeg',
    ];

    /** @var array<int,string> */
    public const ALLOWED_EXTENSIONS = ['ico', 'png', 'webp', 'jpg', 'jpeg'];

    /**
     * @return array{path:string,type:string,version:int,size:int}|null
     */
    public static function current(): ?array
    {
        foreach (self::FILES as $fileName) {
            $absolutePath = public_path($fileName);

            if (! is_file($absolutePath) || filesize($absolutePath) <= 0) {
                continue;
            }

            return [
                'path' => $fileName,
                'type' => self::contentTypeFor($fileName),
                'version' => filemtime($absolutePath) ?: time(),
                'size' => filesize($absolutePath) ?: 0,
            ];
        }

        return null;
    }

    public static function store(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: '');

        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \InvalidArgumentException('Please upload a valid favicon file: .ico, .png, .webp, .jpg, or .jpeg.');
        }

        self::deleteExistingFiles();

        $fileName = 'favicon.'.$extension;
        $file->move(public_path(), $fileName);

        if ($extension !== 'ico') {
            self::writeBlankIcoFallback();
        }

        return $fileName;
    }

    public static function deleteExistingFiles(): void
    {
        foreach (self::FILES as $fileName) {
            $path = public_path($fileName);

            if (is_file($path)) {
                File::delete($path);
            }
        }
    }

    public static function writeBlankIcoFallback(): void
    {
        File::put(public_path('favicon.ico'), '');
    }

    private static function contentTypeFor(string $fileName): string
    {
        return match (strtolower(pathinfo($fileName, PATHINFO_EXTENSION))) {
            'ico' => 'image/x-icon',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'image/x-icon',
        };
    }
}
