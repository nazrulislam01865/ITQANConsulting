<?php

namespace App\Services\Admin;

use App\Models\HomeSection;
use App\Models\PageSection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AdminNavigationService
{
    /** @return Collection<int, HomeSection> */
    public function homeSections(): Collection
    {
        try {
            if (! Schema::hasTable('home_sections')) {
                return collect();
            }

            return HomeSection::query()
                ->select(['id', 'section_key', 'admin_title', 'label', 'sort_order', 'is_active'])
                ->withCount('items')
                ->ordered()
                ->get();
        } catch (Throwable) {
            return collect();
        }
    }

    /** @return array<string,array{label:string,sections:Collection<int,PageSection>}> */
    public function pageGroups(): array
    {
        $pages = [
            'about' => 'About Page',
            'services' => 'Services Page',
            'works' => 'Works Page',
            'catalog' => 'Catalog Page',
            'contact' => 'Contact Page',
        ];

        $groups = [];

        foreach ($pages as $pageKey => $label) {
            $groups[$pageKey] = [
                'label' => $label,
                'sections' => collect(),
            ];
        }

        try {
            if (! Schema::hasTable('page_sections')) {
                return $groups;
            }

            $sections = PageSection::query()
                ->select(['id', 'page_key', 'section_key', 'admin_title', 'label', 'sort_order', 'is_active'])
                ->withCount('items')
                ->ordered()
                ->get()
                ->groupBy('page_key');

            foreach ($groups as $pageKey => $group) {
                $groups[$pageKey]['sections'] = $sections->get($pageKey, collect());
            }
        } catch (Throwable) {
            return $groups;
        }

        return $groups;
    }
}
