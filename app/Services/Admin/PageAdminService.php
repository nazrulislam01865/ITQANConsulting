<?php

namespace App\Services\Admin;

use App\Models\PageSection;
use App\Models\PageSectionItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PageAdminService
{
    public function __construct(private readonly ImageUploadService $imageUploadService)
    {
    }

    /** @param array<string,mixed> $data */
    public function updateSection(
        PageSection $section,
        array $data,
        ?UploadedFile $qrImage = null
    ): PageSection
    {
        $settings = $section->settings ?: [];

        if (array_key_exists('settings', $data)) {
            $settings = array_merge($settings, $this->cleanSettings($data['settings'] ?? []));
        }

        if (str_ends_with($section->section_key, '_hero')) {
            unset(
                $settings['image'],
                $settings['image_path'],
                $settings['background_image'],
                $settings['background_image_path'],
                $settings['hero_image'],
                $settings['hero_image_path'],
                $settings['banner_image'],
                $settings['banner_image_path']
            );
        }

        if ($section->section_key === 'contact_cta') {
            $settings['qr_image_path'] = $this->imageUploadService->replace(
                $qrImage,
                $settings['qr_image_path'] ?? null,
                'page/contact/digital-contact'
            );
        }

        $section->fill([
            'label' => $data['label'] ?? null,
            'title' => $this->cleanLimitedHtml($data['title'] ?? null),
            'lead' => $data['lead'] ?? null,
            'description' => $this->cleanLimitedHtml($data['description'] ?? null),
            'button_text' => $data['button_text'] ?? null,
            'button_route' => $data['button_route'] ?? null,
            'button_url' => $data['button_url'] ?? null,
            'settings' => $settings,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ])->save();

        return $section;
    }

    /** @param array<string,mixed> $data */
    /**
     * @param array<string,mixed> $data
     * @param array<string,\Illuminate\Http\UploadedFile|null> $files
     */
    public function createItem(PageSection $section, array $data, array $files = []): PageSectionItem
    {
        return $section->items()->create($this->normalizeItem(
            $data,
            $this->nextItemSortOrder($section, $data['item_type'] ?? 'card'),
            $files
        ));
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,\Illuminate\Http\UploadedFile|null> $files
     */
    public function updateItem(PageSectionItem $item, array $data, array $files = []): PageSectionItem
    {
        $item->update($this->normalizeItem($data, $item->sort_order, $files, $item));

        return $item;
    }

    public function deleteItem(PageSectionItem $item): void
    {
        $item->delete();
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,\Illuminate\Http\UploadedFile|null> $files
     */
    private function normalizeItem(array $data, int $sortOrder, array $files = [], ?PageSectionItem $existingItem = null): array
    {
        $itemType = $data['item_type'] ?? 'card';
        $settings = $this->cleanSettings($data['settings'] ?? []);

        if ($existingItem && is_array($existingItem->settings)) {
            $settings = array_merge($existingItem->settings, $settings);
        }

        if ($itemType === 'filter') {
            $settings['value'] = Str::slug((string) ($data['title'] ?? ''));
        }

        if ($itemType === 'work') {
            $settings['image_path'] = $this->imageUploadService->replace(
                $files['image'] ?? null,
                $settings['image_path'] ?? null,
                'page/work-cards'
            );
        }

        if ($itemType === 'catalog_page') {
            $settings['image_path'] = $this->imageUploadService->replace(
                $files['media_image'] ?? null,
                $settings['image_path'] ?? null,
                'page/catalog/images'
            );
            $settings['video_path'] = $this->imageUploadService->replace(
                $files['media_video'] ?? null,
                $settings['video_path'] ?? null,
                'page/catalog/videos'
            );
            $settings['thumbnail_path'] = $this->imageUploadService->replace(
                $files['thumbnail'] ?? null,
                $settings['thumbnail_path'] ?? null,
                'page/catalog/thumbnails'
            );
        }

        return [
            'item_type' => $itemType,
            'badge' => $data['badge'] ?? null,
            'title' => $data['title'] ?? null,
            'text' => isset($data['text']) ? $this->cleanLimitedHtml($data['text']) : null,
            'subtitle' => $data['subtitle'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'button_route' => $data['button_route'] ?? null,
            'button_url' => $data['button_url'] ?? null,
            'button_class' => $data['button_class'] ?? null,
            'settings' => $settings,
            'sort_order' => $sortOrder,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
    }

    private function nextItemSortOrder(PageSection $section, string $itemType): int
    {
        $maxSort = $section->items()->where('item_type', $itemType)->max('sort_order');

        if ($maxSort === null) {
            $maxSort = $section->items()->max('sort_order') ?? 0;
        }

        return ((int) $maxSort) + 10;
    }

    /** @param array<string,mixed> $settings */
    private function cleanSettings(array $settings): array
    {
        $clean = [];

        foreach ($settings as $key => $value) {
            $clean[$key] = is_string($value) ? trim($value) : $value;
        }

        return $clean;
    }

    private function cleanLimitedHtml(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(strip_tags($value, '<br><span><strong><b><em><i>'));
    }
}
