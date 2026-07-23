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
            return $this->applyWorkOrderKeys($content);
        }

        $content = $this->applySiteSettings($content);
        $content = $this->applyMenus($content);
        $content = $this->applySocialLinks($content);
        $content = $this->applyHomeFromDatabase($content);
        $content = $this->applyPagesFromDatabase($content);

        return $this->applyWorkOrderKeys($content);
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
        $allSections = HomeSection::query()
            ->with(['activeItems'])
            ->ordered()
            ->get()
            ->keyBy('section_key');

        if ($allSections->isEmpty()) {
            return $content;
        }

        $activeSections = $allSections->filter(fn (HomeSection $section): bool => $section->is_active);
        $content = $this->applyHomeSections($content, $activeSections);

        if ($testimonialSection = $allSections->get('home_testimonials')) {
            $content['pages']['home']['testimonials']['is_active'] = (bool) $testimonialSection->is_active;
        }

        if ($valuesSection = $allSections->get('home_values')) {
            $content['pages']['home']['values']['is_active'] = (bool) $valuesSection->is_active;
        }

        if ($ctaSection = $allSections->get('home_cta')) {
            $content['pages']['home']['cta']['is_active'] = (bool) $ctaSection->is_active;
        }

        return $content;
    }

    /** @param array<string,mixed> $content */
    private function applyPagesFromDatabase(array $content): array
    {
        if (! Schema::hasTable('page_sections') || ! Schema::hasTable('page_section_items')) {
            return $content;
        }

        $allSections = PageSection::query()
            ->with(['activeItems'])
            ->ordered()
            ->get()
            ->groupBy('page_key')
            ->map(fn (Collection $items) => $items->keyBy('section_key'));

        if ($allSections->isEmpty()) {
            return $content;
        }

        $activeSections = $allSections->map(
            fn (Collection $items) => $items->filter(fn (PageSection $section): bool => $section->is_active)
        );

        $content = $this->applyAboutSections($content, $activeSections->get('about', collect()));
        $content = $this->applyServicesSections($content, $activeSections->get('services', collect()));
        $content = $this->applyWorksSections($content, $activeSections->get('works', collect()));
        $content = $this->applyCatalogSections($content, $activeSections->get('catalog', collect()));
        $content = $this->applyContactSections($content, $activeSections->get('contact', collect()));

        if ($contactForm = $allSections->get('contact', collect())->get('contact_form')) {
            $content['pages']['contact']['form']['is_active'] = (bool) $contactForm->is_active;
        }

        if ($contactCta = $allSections->get('contact', collect())->get('contact_cta')) {
            $content['pages']['contact']['cta']['is_active'] = (bool) $contactCta->is_active;
        }

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
                'button' => [
                    'text' => $section->button_text ?: ($home['founder']['button']['text'] ?? 'View my digital resume'),
                    'route' => $section->button_route ?: ($home['founder']['button']['route'] ?? 'starpmaminul.portfolio'),
                    'url' => $section->button_url ?: ($home['founder']['button']['url'] ?? null),
                ],
            ];
        }

        if ($section = $sections->get('home_who')) {
            $whyItems = $section->activeItems
                ->where('item_type', 'card')
                ->values()
                ->map(function ($item, int $index): array {
                    $settings = is_array($item->settings) ? $item->settings : [];

                    return [
                        'num' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                        'title' => $item->title,
                        'text' => $item->text,
                        'response' => trim((string) ($settings['response'] ?? 'We help organize the work into a clearer and more practical system.')),
                        'stage_label' => trim((string) ($settings['stage_label'] ?? $item->title ?? 'A clearer way of working')),
                    ];
                })
                ->all();

            $home['who'] = [
                'label' => $section->label,
                'title' => $section->title,
                'lead' => $section->lead,
                'cards' => $whyItems,
            ];
        }

        if ($section = $sections->get('home_problems')) {
            $clarityItems = $section->activeItems
                ->where('item_type', 'problem')
                ->map(function ($item): array {
                    $settings = is_array($item->settings) ? $item->settings : [];
                    $summary = trim((string) ($settings['summary'] ?? $settings['response'] ?? $item->text ?? ''));
                    $services = $this->lines((string) ($settings['services'] ?? ''));

                    if ($services === [] && filled($settings['response'] ?? null)) {
                        $services = [$settings['response']];
                    }

                    return [
                        'problem' => trim((string) ($settings['problem'] ?? $item->title ?? '')),
                        'summary' => $summary,
                        'services' => $services,
                    ];
                })
                ->filter(fn (array $item): bool => $item['problem'] !== '')
                ->values()
                ->all();

            $home['problems']['label'] = $section->label ?: ($home['problems']['label'] ?? 'Interactive clarity check');
            $home['problems']['title'] = $section->title ?: ($home['problems']['title'] ?? 'Where does the work feel unclear?');
            $home['problems']['lead'] = $section->lead ?: ($home['problems']['lead'] ?? 'Choose the situation that sounds familiar.');

            if ($clarityItems !== []) {
                $home['problems']['items'] = $clarityItems;
            }
        }

        if ($section = $sections->get('home_services_preview')) {
            $serviceItems = $section->activeItems
                ->where('item_type', 'service_card')
                ->values()
                ->map(function ($item, int $index): array {
                    $settings = is_array($item->settings) ? $item->settings : [];

                    return [
                        'num' => $item->badge ?: str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                        'title' => $item->title,
                        'text' => $item->text,
                        'common_problem' => trim((string) ($settings['common_problem'] ?? 'The work lacks a clear structure, owner, or practical next step.')),
                        'deliverables' => trim((string) ($settings['deliverables'] ?? 'A clear scope, practical recommendations, and an actionable delivery plan.')),
                    ];
                })
                ->all();

            $home['services_preview'] = [
                'label' => $section->label,
                'title' => $section->title,
                'lead' => $section->lead,
                'items' => $serviceItems,
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
                'is_active' => true,
                'label' => $section->label,
                'title' => $section->title,
                'lead' => $section->lead,
            ];
            $testimonials = $section->activeItems->where('item_type', 'testimonial')->map(function ($item): array {
                $settings = is_array($item->settings) ? $item->settings : [];

                return [
                    'title' => $item->title,
                    'text' => $item->text,
                    'author' => $settings['author'] ?? '',
                    'role' => $settings['role'] ?? '',
                    'project' => $settings['project'] ?? '',
                ];
            })->values()->all();
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

        if ($section = $sections->get('home_values')) {
            $valueItems = $section->activeItems
                ->where('item_type', 'value')
                ->values()
                ->map(function ($item, int $index): array {
                    $settings = is_array($item->settings) ? $item->settings : [];
                    $number = $item->badge ?: str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

                    return [
                        'num' => str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                        'mini' => trim((string) ($settings['mini'] ?? ($number . ' / principle'))),
                        'title' => $item->title,
                        'text' => $item->text,
                        'example' => trim((string) ($settings['example'] ?? '')),
                    ];
                })
                ->all();

            $home['values'] = [
                'is_active' => true,
                'label' => $section->label,
                'title' => $section->title,
                'lead' => $section->lead,
                'items' => $valueItems,
            ];
        }

        if ($section = $sections->get('home_cta')) {
            $settings = $section->settings ?: [];
            $defaults = $home['cta'];
            $defaultVcard = $defaults['vcard'] ?? [];
            $qrImageUrl = $this->storageUrl($settings['qr_image_path'] ?? null)
                ?: asset(ltrim((string) ($defaults['qr_image_url'] ?? '/images/default-itqan-contact-qr.png'), '/'));

            $home['cta'] = [
                'is_active' => true,
                'eyebrow' => $section->label ?: ($defaults['eyebrow'] ?? 'Start with a conversation'),
                'title' => $section->title ?: ($defaults['title'] ?? ''),
                'text' => $section->lead ?: ($defaults['text'] ?? ''),
                'button' => $this->sectionButton($section, 'contact'),
                'save_button_text' => $settings['save_button_text'] ?? ($defaults['save_button_text'] ?? 'Save Contact'),
                'qr_image_url' => $qrImageUrl,
                'qr_alt' => $settings['qr_alt'] ?? ($defaults['qr_alt'] ?? 'Digital contact QR code'),
                'qr_caption' => $settings['qr_caption'] ?? ($defaults['qr_caption'] ?? ''),
                'contact_file_name' => $settings['contact_file_name'] ?? ($defaults['contact_file_name'] ?? 'ITQAN-Consulting-Digital-Card.vcf'),
                'vcard' => [
                    'first_name' => $settings['first_name'] ?? ($defaultVcard['first_name'] ?? ''),
                    'last_name' => $settings['last_name'] ?? ($defaultVcard['last_name'] ?? ''),
                    'full_name' => $settings['full_name'] ?? ($defaultVcard['full_name'] ?? ''),
                    'credentials' => $settings['credentials'] ?? ($defaultVcard['credentials'] ?? ''),
                    'organization' => $settings['organization'] ?? ($defaultVcard['organization'] ?? ''),
                    'job_title' => $settings['job_title'] ?? ($defaultVcard['job_title'] ?? ''),
                    'phone' => $settings['phone'] ?? ($defaultVcard['phone'] ?? ''),
                    'whatsapp' => $settings['whatsapp'] ?? ($defaultVcard['whatsapp'] ?? ''),
                    'email' => $settings['email'] ?? ($defaultVcard['email'] ?? ''),
                    'website' => $settings['website'] ?? ($defaultVcard['website'] ?? ''),
                    'note' => $settings['note'] ?? ($defaultVcard['note'] ?? ''),
                ],
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
            $settings = $section->settings ?: [];
            $defaults = $contactPage['form'] ?? [];
            $defaultSteps = $defaults['steps'] ?? [];
            $defaultOptions = $content['collections']['contact_options'] ?? [];

            $optionLines = function (string $key, array $fallback) use ($settings): array {
                $lines = $this->lines((string) ($settings[$key] ?? ''));

                return $lines !== [] ? $lines : $fallback;
            };

            $contactPage['form'] = [
                'is_active' => true,
                'label' => $section->label ?: ($defaults['label'] ?? 'Start a conversation'),
                'title' => $section->title ?: ($defaults['title'] ?? ''),
                'intro' => $section->lead ?: ($defaults['intro'] ?? ''),
                'steps' => [
                    [
                        'title' => $settings['problem_step_title'] ?? ($defaultSteps[0]['title'] ?? 'What feels unclear right now?'),
                        'text' => $settings['problem_step_text'] ?? ($defaultSteps[0]['text'] ?? ''),
                    ],
                    [
                        'title' => $settings['support_step_title'] ?? ($defaultSteps[1]['title'] ?? 'What kind of support may help?'),
                        'text' => $settings['support_step_text'] ?? ($defaultSteps[1]['text'] ?? ''),
                    ],
                    [
                        'title' => $settings['details_step_title'] ?? ($defaultSteps[2]['title'] ?? 'Share the basic details.'),
                        'text' => $settings['details_step_text'] ?? ($defaultSteps[2]['text'] ?? ''),
                    ],
                    [
                        'title' => $settings['message_step_title'] ?? ($defaultSteps[3]['title'] ?? 'Describe the situation in your own words.'),
                        'text' => $settings['message_step_text'] ?? ($defaultSteps[3]['text'] ?? ''),
                    ],
                ],
                'submit_text' => $settings['submit_text'] ?? ($defaults['submit_text'] ?? 'Send the messy version'),
                'success_title' => $settings['success_title'] ?? ($defaults['success_title'] ?? 'Thank you. The first step is clear.'),
                'success_text' => $settings['success_text'] ?? ($defaults['success_text'] ?? 'Your message has been received.'),
            ];

            $content['collections']['contact_options'] = [
                'problems' => $optionLines('problems', $defaultOptions['problems'] ?? []),
                'needs' => $optionLines('needs', $defaultOptions['needs'] ?? []),
                'methods' => $optionLines('methods', $defaultOptions['methods'] ?? []),
                'budgets' => $optionLines('budgets', $defaultOptions['budgets'] ?? []),
            ];
        }

        if ($section = $sections->get('contact_cta')) {
            $settings = $section->settings ?: [];
            $defaults = $contactPage['cta'];
            $defaultVcard = $defaults['vcard'] ?? [];
            $qrImageUrl = $this->storageUrl($settings['qr_image_path'] ?? null)
                ?: asset(ltrim((string) ($defaults['qr_image_url'] ?? '/images/default-itqan-contact-qr.png'), '/'));

            $contactPage['cta'] = [
                'is_active' => true,
                'eyebrow' => $section->label ?: ($defaults['eyebrow'] ?? 'Save the contact'),
                'title' => $section->title ?: ($defaults['title'] ?? ''),
                'text' => $section->lead ?: ($defaults['text'] ?? ''),
                'button' => $this->sectionButton($section, 'contact'),
                'save_button_text' => $settings['save_button_text'] ?? ($defaults['save_button_text'] ?? 'Save Contact'),
                'qr_image_url' => $qrImageUrl,
                'qr_alt' => $settings['qr_alt'] ?? ($defaults['qr_alt'] ?? 'Digital contact QR code'),
                'qr_caption' => $settings['qr_caption'] ?? ($defaults['qr_caption'] ?? ''),
                'contact_file_name' => $settings['contact_file_name'] ?? ($defaults['contact_file_name'] ?? 'md-aminul-islam-itqan-consulting.vcf'),
                'vcard' => [
                    'first_name' => $settings['first_name'] ?? ($defaultVcard['first_name'] ?? ''),
                    'last_name' => $settings['last_name'] ?? ($defaultVcard['last_name'] ?? ''),
                    'full_name' => $settings['full_name'] ?? ($defaultVcard['full_name'] ?? ''),
                    'credentials' => $settings['credentials'] ?? ($defaultVcard['credentials'] ?? ''),
                    'organization' => $settings['organization'] ?? ($defaultVcard['organization'] ?? ''),
                    'job_title' => $settings['job_title'] ?? ($defaultVcard['job_title'] ?? ''),
                    'phone' => $settings['phone'] ?? ($defaultVcard['phone'] ?? ''),
                    'whatsapp' => $settings['whatsapp'] ?? ($defaultVcard['whatsapp'] ?? ''),
                    'email' => $settings['email'] ?? ($defaultVcard['email'] ?? ''),
                    'website' => $settings['website'] ?? ($defaultVcard['website'] ?? ''),
                    'note' => $settings['note'] ?? ($defaultVcard['note'] ?? ''),
                ],
            ];
        }

        $content['pages']['contact'] = $contactPage;

        return $content;
    }

    /** @param array<string,mixed> $content */
    private function applyWorkOrderKeys(array $content): array
    {
        $works = collect($content['collections']['works'] ?? [])
            ->values()
            ->map(function (array $work, int $index): array {
                if (blank($work['order_key'] ?? null)) {
                    $slug = str((string) ($work['title'] ?? 'work-item'))->slug()->toString();
                    $work['order_key'] = 'catalog-'.($slug !== '' ? $slug : 'work').'-'.($index + 1);
                }

                return $work;
            })
            ->all();

        $content['collections']['works'] = $works;

        if (isset($content['collections']['home_featured_works'])) {
            $content['collections']['home_featured_works'] = collect($content['collections']['home_featured_works'])
                ->values()
                ->map(function (array $work, int $index): array {
                    if (blank($work['order_key'] ?? null)) {
                        $slug = str((string) ($work['title'] ?? 'work-item'))->slug()->toString();
                        $work['order_key'] = 'featured-'.($slug !== '' ? $slug : 'work').'-'.($index + 1);
                    }

                    return $work;
                })
                ->all();
        }

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
            'id' => $item->getKey(),
            'order_key' => 'work-'.$item->getKey(),
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
