<?php

namespace App\Services\Admin;

use App\Models\NavigationMenuItem;

class NavigationMenuService
{
    /** @param array<string,mixed> $data */
    public function create(array $data): NavigationMenuItem
    {
        return NavigationMenuItem::query()->create($this->normalize($data, $this->nextSortOrder()));
    }

    /** @param array<string,mixed> $data */
    public function update(NavigationMenuItem $item, array $data): NavigationMenuItem
    {
        $item->update($this->normalize($data, $item->sort_order));

        return $item;
    }

    public function delete(NavigationMenuItem $item): void
    {
        $item->delete();
    }

    /** @param array<string,mixed> $data */
    private function normalize(array $data, int $sortOrder): array
    {
        return [
            'label' => $data['label'],
            'route_name' => $data['route_name'] ?: null,
            'url' => $data['url'] ?: null,
            'sort_order' => $sortOrder,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
    }

    private function nextSortOrder(): int
    {
        return ((int) (NavigationMenuItem::query()->max('sort_order') ?? 0)) + 10;
    }
}
