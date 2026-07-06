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
    public function updateSection(HomeSection $section, array $data, ?UploadedFile $founderImage = null): HomeSection
    {
        $settings = $section->settings ?: [];

        if (array_key_exists('settings', $data)) {
            $settings = array_merge($settings, array_filter($data['settings'] ?? [], fn ($value) => $value !== null));
        }

        if ($section->section_key === 'home_founder') {
            $settings['image_path'] = $this->imageUploadService->replace(
                $founderImage,
                $settings['image_path'] ?? null,
                'home/founder'
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
        return $section->items()->create($this->normalizeItem(
            $data,
            $this->nextItemSortOrder($section, $data['item_type'] ?? 'card'),
            $image
        ));
    }

    /** @param array<string,mixed> $data */
    public function updateItem(HomeSectionItem $item, array $data, ?UploadedFile $image = null): HomeSectionItem
    {
        $item->update($this->normalizeItem($data, $item->sort_order, $image, $item));

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
