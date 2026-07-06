<?php

namespace App\Services\Admin;

use App\Models\SiteSetting;
use Illuminate\Http\UploadedFile;

class SiteSettingsService
{
    public function __construct(private readonly ImageUploadService $imageUploadService)
    {
    }

    /**
     * @param  array<string,mixed>  $data
     */
    public function update(array $data, ?UploadedFile $logo = null): SiteSetting
    {
        $settings = SiteSetting::query()->firstOrCreate([], [
            'site_name' => 'ITQAN Consulting',
            'mark_text' => 'IC',
        ]);

        $settings->fill($data);
        $settings->logo_path = $this->imageUploadService->replace($logo, $settings->logo_path, 'site');
        $settings->save();

        return $settings;
    }
}
