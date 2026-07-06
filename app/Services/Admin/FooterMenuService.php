<?php

namespace App\Services\Admin;

use App\Models\FooterMenuItem;

class FooterMenuService
{
    /** @param array<string,mixed> $data */
    public function create(array $data): FooterMenuItem
    {
        return FooterMenuItem::query()->create($this->normalize($data, $this->nextSortOrder($data['group_key'] ?? 'pages')));
    }

    /** @param array<string,mixed> $data */
    public function update(FooterMenuItem $item, array $data): FooterMenuItem
    {
        $item->update($this->normalize($data, $item->sort_order));

        return $item;
    }

    public function delete(FooterMenuItem $item): void
    {
        $item->delete();
    }

    /** @param array<string,mixed> $data */
    private function normalize(array $data, int $sortOrder): array
    {
        return [
            'group_key' => $data['group_key'] ?: 'pages',
            'group_title' => $data['group_title'] ?: 'Pages',
            'label' => $data['label'],
            'route_name' => $data['route_name'] ?: null,
            'url' => $data['url'] ?: null,
            'sort_order' => $sortOrder,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
    }

    private function nextSortOrder(string $groupKey): int
    {
        return ((int) (FooterMenuItem::query()
            ->where('group_key', $groupKey ?: 'pages')
            ->max('sort_order') ?? 0)) + 10;
    }
}
