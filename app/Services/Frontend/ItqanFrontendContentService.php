<?php

namespace App\Services\Frontend;

use App\Models\FooterMenuItem;
use App\Models\HomeSection;
use App\Models\NavigationMenuItem;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ItqanFrontendContentService
{
    /** @return array<string,mixed> */
    public function content(): array
    {
        $content = config('itqan');

        if (! $this->databaseReady()) {
            return $content;
        }

        $content = $this->applySiteSettings($content);
        $content = $this->applyMenus($content);
        $content = $this->applySocialLinks($content);
        $content = $this->applyHomeFromDatabase($content);
        $content = $this->applyPagesFromDatabase($content);

        return $content;
    }

    private function databaseReady(): bool
    {
        return Schema::hasTable('site_settings')
            && Schema::hasTable('navigation_menu_items')
            && Schema::hasTable('footer_menu_items')
            && Schema::hasTable('home_sections')
            && Schema::hasTable('home_section_items');
    }

    /** @param array<string,mixed> $content */
    private function applySiteSettings(array $content): array
    {
        $site = SiteSetting::current();

        if (! $site) {
            return $content;
        }

        $content['site'] = array_merge($content['site'], [
            'name' => $site->site_name,
            'mark' => $site->mark_text ?: 'IC',
            'logo_url' => $site->logoUrl(),
            'tagline' => $site->tagline ?: '',
            'email' => $site->email ?: '',
            'address' => $site->address ?: '',
            'description' => $site->description ?: '',
            'primary_cta' => [
                'text' => $site->primary_cta_text ?: 'Book a Consultation',
                'route' => $site->primary_cta_route ?: 'contact',
            ],
        ]);

        $content['footer']['bottom_left'] = $site->footer_bottom_left ?: $content['footer']['bottom_left'];
        $content['footer']['copyright'] = $site->copyright ?: $content['footer']['copyright'];

        return $content;
    }

    /** @param array<string,mixed> $content */
    private function applyMenus(array $content): array
    {
        $navigation = NavigationMenuItem::query()
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->map(fn (NavigationMenuItem $item): array => [
                'label' => $item->label,
                'route' => $item->route_name,
                'url' => $item->url,
            ])
            ->all();

        if ($navigation !== []) {
            $content['navigation'] = $navigation;
        }

        $footerGroups = FooterMenuItem::query()
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->groupBy('group_key')
            ->map(fn ($items) => [
                'title' => $items->first()->group_title,
                'items' => $items->map(fn (FooterMenuItem $item): array => [
                    'label' => $item->label,
                    'route' => $item->route_name,
                    'url' => $item->url,
                ])->values()->all(),
            ])
            ->all();

        if ($footerGroups !== []) {
            $content['footer']['menus'] = $footerGroups;
        }

        return $content;
    }


    /** @param array<string,mixed> $content */
    private function applySocialLinks(array $content): array
    {
        if (! Schema::hasTable('social_links')) {
            return $content;
        }

        $links = SocialLink::query()
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->map(fn (SocialLink $link): array => [
                'platform' => $link->platform,
                'label' => $link->label,
                'url' => $link->url ?: '#',
                'icon_url' => $link->resolvedIconUrl(),
                'icon_source' => $link->iconSourceLabel(),
            ])
            ->values()
            ->all();

        if ($links !== []) {
            $content['social_links'] = $links;
        }

        return $content;
    }

    /** @param array<string,mixed> $content */
    private function applyHomeFromDatabase(array $content): array
    {
        $sections = HomeSection::query()
            ->with(['activeItems'])
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->keyBy('section_key');

        if ($sections->isEmpty()) {
            return $content;
        }

        return $this->applyHomeSections($content, $sections);
    }

    /** @param array<string,mixed> $content */
    private function applyPagesFromDatabase(array $content): array
    {
        if (! Schema::hasTable('page_sections') || ! Schema::hasTable('page_section_items')) {
            return $content;
        }

        $sections = PageSection::query()
            ->with(['activeItems'])
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->groupBy('page_key')
            ->map(fn (Collection $items) => $items->keyBy('section_key'));

        if ($sections->isEmpty()) {
            return $content;
        }

        $content = $this->applyAboutSections($content, $sections->get('about', collect()));
        $content = $this->applyServicesSections($content, $sections->get('services', collect()));
        $content = $this->applyWorksSections($content, $sections->get('works', collect()));
        $content = $this->applyCatalogSections($content, $sections->get('catalog', collect()));
        $content = $this->applyContactSections($content, $sections->get('contact', collect()));

        return $content;
    }

    /** @param Collection<string,HomeSection> $sections */
    private function applyHomeSections(array $content, Collection $sections): array
    {
        $home = $content['pages']['home'];

        if ($section = $sections->get('home_hero')) {
            $settings = $section->settings ?: [];
            $home['hero'] = [
                'label' => $section->label,
                'title' => $section->title,
                'description' => $section->description,
                'social_label' => $settings['social_label'] ?? 'Connect with ITQAN',
                'chips' => $section->activeItems->where('item_type', 'chip')->pluck('title')->values()->all(),
                'buttons' => $this->itemsToButtons($section, 'button'),
                'ticker' => $section->activeItems->where('item_type', 'ticker')->pluck('text')->values()->all(),
            ];

        }

        if ($section = $sections->get('home_founder')) {
            $settings = $section->settings ?: [];
            $bodyText = trim((string) ($section->description ?? ''));
            $paragraphs = $bodyText !== ''
                ? $this->lines($bodyText)
                : $section->activeItems->where('item_type', 'paragraph')->pluck('text')->values()->all();

            $home['founder'] = [
                'label' => $section->label,
                'title' => $section->title,
                'paragraphs' => array_values(array_filter(array_map('trim', $paragraphs ?: []))),
                'image_url' => $this->storageUrl($settings['image_path'] ?? null),
                'name' => $settings['name'] ?? '',
                'role' => $settings['role'] ?? '',
            ];
        }

        if ($section = $sections->get('home_who')) {
            $home['who'] = [
                'label' => $section->label,
                'title' => $section->title,
                'lead' => $section->lead,
                'cards' => $this->cards($section, 'card', true),
            ];
        }

        if ($section = $sections->get('home_problems')) {
            $home['problems'] = [
                'label' => $section->label,
                'title' => $section->title,
                'lead' => $section->lead,
                'items' => $section->activeItems->where('item_type', 'problem')->map(fn ($item) => [
                    'problem' => $item->settings['problem'] ?? $item->title,
                    'response' => $item->settings['response'] ?? $item->text,
                ])->values()->all(),
            ];
        }

        if ($section = $sections->get('home_services_preview')) {
            $home['services_preview'] = [
                'label' => $section->label,
                'title' => $section->title,
                'lead' => $section->lead,
                'items' => $this->cards($section, 'service_card'),
                'button' => $this->sectionButton($section, 'services'),
            ];
        }

        if ($section = $sections->get('home_working')) {
            $home['working'] = [
                'label' => $section->label,
                'title' => $section->title,
                'intro' => $section->lead,
                'items' => $this->cards($section, 'step'),
            ];
        }

        if ($section = $sections->get('home_testimonials')) {
            $home['testimonials'] = [
                'label' => $section->label,
                'title' => $section->title,
            ];
            $testimonials = $section->activeItems->where('item_type', 'testimonial')->map(fn ($item) => [
                'title' => $item->title,
                'text' => $item->text,
                'author' => $item->settings['author'] ?? '',
                'role' => $item->settings['role'] ?? '',
            ])->values()->all();
            if ($testimonials !== []) {
                $content['collections']['testimonials'] = $testimonials;
            }
        }

        if ($section = $sections->get('home_works_preview')) {
            $home['works_preview'] = [
                'label' => $section->label,
                'title' => $section->title,
                'button' => $this->sectionButton($section, 'works'),
            ];
        }

        if ($section = $sections->get('home_cta')) {
            $home['cta'] = [
                'title' => $section->title,
                'text' => $section->lead,
                'button' => $this->sectionButton($section, 'contact'),
            ];
        }

        $content['pages']['home'] = $home;

        return $content;
    }

    /** @param Collection<string,PageSection> $sections */
    private function applyAboutSections(array $content, Collection $sections): array
    {
        if ($sections->isEmpty()) {
            return $content;
        }

        $about = $content['pages']['about'];

        if ($section = $sections->get('about_hero')) {
            $about['hero'] = $this->hero($section);
        }
        if ($section = $sections->get('about_story')) {
            $about['story'] = [
                'label' => $section->label,
                'title' => $section->title,
                'paragraphs' => $this->lines((string) $section->description),
            ];
        }
        if ($section = $sections->get('about_beliefs')) {
            $about['beliefs'] = [
                'label' => $section->label,
                'title' => $section->title,
                'items' => $this->cards($section, 'card', true),
            ];
        }
        if ($section = $sections->get('about_mission_vision')) {
            $about['mission_vision'] = $section->activeItems->where('item_type', 'mission_card')->values()->map(fn ($item) => [
                'num' => $item->badge,
                'title' => $item->title,
                'text' => $item->text,
            ])->values()->all();
        }
        if ($section = $sections->get('about_cta')) {
            $about['cta'] = $this->cta($section, 'contact');
        }

        $content['pages']['about'] = $about;

        return $content;
    }

    /** @param Collection<string,PageSection> $sections */
    private function applyServicesSections(array $content, Collection $sections): array
    {
        if ($sections->isEmpty()) {
            return $content;
        }

        $servicesPage = $content['pages']['services'];

        if ($section = $sections->get('services_hero')) {
            $servicesPage['hero'] = $this->hero($section);
        }
        if ($section = $sections->get('services_areas')) {
            $content['collections']['services'] = $section->activeItems->where('item_type', 'service_area')->values()->map(fn ($item, int $index) => [
                'badge' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'title' => $item->title,
                'intro' => $item->subtitle,
                'points' => $this->lines((string) ($item->settings['points'] ?? '')),
                'button' => $item->button_text ?: 'Discuss',
                'button_route' => $item->button_route,
                'button_url' => $item->button_url,
            ])->values()->all();
        }
        if ($section = $sections->get('services_faq')) {
            $servicesPage['faq_title'] = $section->title;
            $content['collections']['service_faqs'] = $section->activeItems->where('item_type', 'faq')->values()->map(fn ($item) => [
                'question' => $item->title,
                'summary' => $item->subtitle,
                'answer' => $this->lines((string) $item->text),
            ])->values()->all();
        }
        if ($section = $sections->get('services_cta')) {
            $servicesPage['cta'] = $this->cta($section, 'contact');
        }

        $content['pages']['services'] = $servicesPage;

        return $content;
    }

    /** @param Collection<string,PageSection> $sections */
    private function applyWorksSections(array $content, Collection $sections): array
    {
        if ($sections->isEmpty()) {
            return $content;
        }

        $worksPage = $content['pages']['works'];

        if ($section = $sections->get('works_hero')) {
            $worksPage['hero'] = $this->hero($section);
        }
        if ($section = $sections->get('works_grid')) {
            $filters = $section->activeItems->where('item_type', 'filter')->values()->map(fn ($item) => [
                'label' => $item->title,
                'value' => $item->settings['value'] ?? str($item->title)->slug()->toString(),
            ])->values()->all();

            if ($filters !== []) {
                $content['collections']['work_filters'] = $filters;
            }

            $works = $section->activeItems
                ->where('item_type', 'work')
                ->values()
                ->map(fn ($item) => $this->workItem($item))
                ->values()
                ->all();

            if ($works !== []) {
                $content['collections']['works'] = $works;

                $hasFeaturedSetting = collect($works)->contains(fn (array $work): bool => (bool) ($work['has_featured_setting'] ?? false));
                $featuredWorks = collect($works)
                    ->filter(fn (array $work): bool => (bool) ($work['featured_on_home'] ?? false))
                    ->values()
                    ->all();

                $content['collections']['home_featured_works'] = $hasFeaturedSetting
                    ? $featuredWorks
                    : array_slice($works, 0, 4);
            }
        }

        $content['pages']['works'] = $worksPage;

        return $content;
    }

    /** @param Collection<string,PageSection> $sections */
    private function applyCatalogSections(array $content, Collection $sections): array
    {
        if ($sections->isEmpty()) {
            return $content;
        }

        $catalogPage = $content['pages']['catalog'];

        if ($section = $sections->get('catalog_hero')) {
            $catalogPage['hero'] = $this->hero($section);
        }
        if ($section = $sections->get('catalog_viewer')) {
            $catalogPage['viewer'] = [
                'eyebrow' => $section->settings['viewer_eyebrow'] ?? 'Catalog Viewer',
                'title' => $section->settings['viewer_title'] ?? 'ITQAN Service Profile',
            ];

            $content['collections']['catalog_pages'] = $section->activeItems->where('item_type', 'catalog_page')->values()->map(function ($item): array {
                $settings = is_array($item->settings) ? $item->settings : [];

                return [
                    'type' => $settings['type'] ?? 'image',
                    'title' => $item->title,
                    'kicker' => $item->subtitle,
                    'body' => $item->text,
                    'image_path' => $settings['image_path'] ?? null,
                    'image_url' => $this->storageUrl($settings['image_path'] ?? null),
                    'video_path' => $settings['video_path'] ?? null,
                    'video_url' => $this->storageUrl($settings['video_path'] ?? null),
                    'thumbnail_path' => $settings['thumbnail_path'] ?? null,
                    'thumbnail_url' => $this->storageUrl($settings['thumbnail_path'] ?? null),
                ];
            })->values()->all();
        }

        $content['pages']['catalog'] = $catalogPage;

        return $content;
    }

    /** @param Collection<string,PageSection> $sections */
    private function applyContactSections(array $content, Collection $sections): array
    {
        if ($sections->isEmpty()) {
            return $content;
        }

        $contactPage = $content['pages']['contact'];

        if ($section = $sections->get('contact_hero')) {
            $contactPage['hero'] = $this->hero($section);
        }
        if ($section = $sections->get('contact_form')) {
            $contactPage['side_note'] = [
                'label' => $section->label,
                'title' => $section->title,
                'text' => $section->lead,
                'steps' => $section->activeItems->where('item_type', 'step')->pluck('title')->values()->all(),
            ];

            $content['collections']['contact_options'] = [
                'needs' => $this->lines((string) ($section->settings['needs'] ?? '')),
                'areas' => $this->lines((string) ($section->settings['areas'] ?? '')),
                'methods' => $this->lines((string) ($section->settings['methods'] ?? '')),
            ];
        }
        if ($section = $sections->get('contact_cta')) {
            $contactPage['cta'] = $this->cta($section, 'contact');
        }

        $content['pages']['contact'] = $contactPage;

        return $content;
    }

    private function hero($section): array
    {
        $hero = [
            'label' => $section->label,
            'title' => $section->title,
            'description' => $section->description,
        ];

        $buttons = $this->itemsToButtons($section, 'button');
        if ($buttons !== []) {
            $hero['buttons'] = $buttons;
        }

        return $hero;
    }

    private function cta($section, string $fallbackRoute): array
    {
        return [
            'title' => $section->title,
            'text' => $section->lead,
            'button' => $this->sectionButton($section, $fallbackRoute),
        ];
    }

    /** @return array<int,string> */
    private function lines(string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R+/', trim($text)) ?: [])));
    }

    private function storageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    private function sectionButton($section, string $fallbackRoute): array
    {
        return [
            'text' => $section->button_text ?: 'Learn More',
            'route' => $section->button_route ?: $fallbackRoute,
            'url' => $section->button_url ?: null,
        ];
    }

    private function itemsToButtons($section, string $type): array
    {
        return $section->activeItems->where('item_type', $type)->map(fn ($item) => [
            'text' => $item->button_text ?: $item->title,
            'route' => $item->button_route,
            'url' => $item->button_url,
            'class' => $item->button_class,
        ])->values()->all();
    }

    private function cards($section, string $type, bool $autoNumber = false): array
    {
        return $section->activeItems->where('item_type', $type)->values()->map(fn ($item, int $index) => [
            'num' => $autoNumber ? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) : $item->badge,
            'title' => $item->title,
            'text' => $item->text,
        ])->values()->all();
    }

    private function workItem($item): array
    {
        $settings = is_array($item->settings) ? $item->settings : [];
        $categories = array_values(array_filter(array_map('trim', explode(',', (string) ($settings['categories'] ?? '')))));
        $tags = array_values(array_filter(array_map('trim', explode(',', (string) ($settings['tags'] ?? '')))));
        $hasFeaturedSetting = array_key_exists('featured_on_home', $settings);

        return [
            'title' => $item->title,
            'pill' => $item->badge,
            'preview_pill' => $item->badge,
            'description' => $item->text,
            'preview_description' => $item->text,
            'categories' => $categories,
            'tags' => $tags,
            'button_text' => $item->button_text ?: 'View Case Study',
            'button_route' => $item->button_route,
            'button_url' => $item->button_url,
            'image_url' => $this->storageUrl($settings['image_path'] ?? null),
            'featured_on_home' => filter_var($settings['featured_on_home'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'has_featured_setting' => $hasFeaturedSetting,
        ];
    }
}
