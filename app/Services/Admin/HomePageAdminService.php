<?php

namespace App\Services\Admin;

use App\Models\HomeSection;
use App\Models\HomeSectionItem;
use Illuminate\Http\UploadedFile;

class HomePageAdminService
{
    public function __construct(private readonly ImageUploadService $imageUploadService)
    {
    }

    /** @param array<string,mixed> $data */
    public function updateSection(
        HomeSection $section,
        array $data,
        ?UploadedFile $founderImage = null,
        ?UploadedFile $qrImage = null
    ): HomeSection
    {
        $settings = $section->settings ?: [];

        if (array_key_exists('settings', $data)) {
            foreach (($data['settings'] ?? []) as $key => $value) {
                $settings[$key] = is_string($value) ? trim($value) : ($value ?? '');
            }
        }

        if ($section->section_key === 'home_hero') {
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

        if ($section->section_key === 'home_founder') {
            $settings['image_path'] = $this->imageUploadService->replace(
                $founderImage,
                $settings['image_path'] ?? null,
                'home/founder'
            );
        }

        if ($section->section_key === 'home_cta') {
            $settings['qr_image_path'] = $this->imageUploadService->replace(
                $qrImage,
                $settings['qr_image_path'] ?? null,
                'home/digital-contact'
            );
        }

        $section->fill([
            'label' => $data['label'] ?? null,
            'title' => $this->cleanLimitedHtml($data['title'] ?? null),
            'lead' => $data['lead'] ?? null,
            'description' => $data['description'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'button_route' => $data['button_route'] ?? null,
            'button_url' => $data['button_url'] ?? null,
            'settings' => $settings,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ])->save();

        return $section;
    }

    /** @param array<string,mixed> $data */
    public function createItem(HomeSection $section, array $data, ?UploadedFile $image = null): HomeSectionItem
    {
        $sortOrder = $this->requestedSortOrder($data)
            ?? $this->nextItemSortOrder($section, $data['item_type'] ?? 'card');

        return $section->items()->create($this->normalizeItem(
            $data,
            $sortOrder,
            $image
        ));
    }

    /** @param array<string,mixed> $data */
    public function updateItem(HomeSectionItem $item, array $data, ?UploadedFile $image = null): HomeSectionItem
    {
        $sortOrder = $this->requestedSortOrder($data) ?? $item->sort_order;
        $item->update($this->normalizeItem($data, $sortOrder, $image, $item));

        return $item;
    }

    public function deleteItem(HomeSectionItem $item): void
    {
        $item->delete();
    }

    /** @param array<string,mixed> $data */
    private function normalizeItem(array $data, int $sortOrder, ?UploadedFile $image = null, ?HomeSectionItem $existingItem = null): array
    {
        $settings = [];

        foreach (($data['settings'] ?? []) as $key => $value) {
            $settings[$key] = is_string($value) ? trim($value) : $value;
        }

        if ($existingItem && is_array($existingItem->settings)) {
            $settings = array_merge($existingItem->settings, $settings);
        }

        $itemType = $data['item_type'] ?? 'card';
        $badge = $data['badge'] ?? null;

        if ($itemType === 'social_link') {
            $badge = $settings['platform'] ?? $badge;
        }

        if ($itemType === 'problem') {
            $data['title'] = $settings['problem'] ?? ($data['title'] ?? null);
            $data['text'] = $settings['summary'] ?? $settings['response'] ?? ($data['text'] ?? null);
        }

        if ($itemType === 'work') {
            $settings['image_path'] = $this->imageUploadService->replace(
                $image,
                $settings['image_path'] ?? null,
                'home/work-cards'
            );
        }

        return [
            'item_type' => $itemType,
            'badge' => $badge,
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


    /** @param array<string,mixed> $data */
    private function requestedSortOrder(array $data): ?int
    {
        if (! array_key_exists('sort_order', $data) || $data['sort_order'] === null || $data['sort_order'] === '') {
            return null;
        }

        return (int) $data['sort_order'];
    }

    private function nextItemSortOrder(HomeSection $section, string $itemType): int
    {
        $maxSort = $section->items()
            ->where('item_type', $itemType)
            ->max('sort_order');

        if ($maxSort === null) {
            $maxSort = $section->items()->max('sort_order') ?? 0;
        }

        return ((int) $maxSort) + 10;
    }

    private function cleanLimitedHtml(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(strip_tags($value, '<br><span><strong><b><em><i>'));
    }
}
