<?php

namespace Database\Seeders;

use App\Models\FooterMenuItem;
use App\Models\HomeSection;
use App\Models\NavigationMenuItem;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class ItqanWebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $content = config('itqan');

        $this->seedSiteSettings($content);
        $this->seedHeaderMenu($content);
        $this->seedFooterMenu($content);
        $this->seedSocialLinks($content);
        $this->seedHomeSections($content);
        $this->seedPageSections($content);
    }

    /** @param array<string,mixed> $content */
    private function seedSiteSettings(array $content): void
    {
        SiteSetting::query()->firstOrCreate([], [
            'site_name' => $content['site']['name'],
            'mark_text' => $content['site']['mark'],
            'tagline' => $content['site']['tagline'],
            'email' => $content['site']['email'],
            'address' => $content['site']['address'],
            'description' => $content['site']['description'],
            'primary_cta_text' => $content['site']['primary_cta']['text'],
            'primary_cta_route' => $content['site']['primary_cta']['route'],
            'footer_bottom_left' => $content['footer']['bottom_left'],
            'copyright' => $content['footer']['copyright'],
        ]);
    }

    /** @param array<string,mixed> $content */
    private function seedHeaderMenu(array $content): void
    {
        foreach ($content['navigation'] as $index => $item) {
            NavigationMenuItem::query()->updateOrCreate(
                ['label' => $item['label']],
                [
                    'route_name' => $item['route'],
                    'url' => null,
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ]
            );
        }
    }

    /** @param array<string,mixed> $content */
    private function seedFooterMenu(array $content): void
    {
        foreach ($content['navigation'] as $index => $item) {
            FooterMenuItem::query()->updateOrCreate(
                ['group_key' => 'pages', 'label' => $item['label'] === 'Catalog' ? 'Digital Catalog' : $item['label']],
                [
                    'group_title' => 'Pages',
                    'route_name' => $item['route'],
                    'url' => null,
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ]
            );
        }

        foreach ($content['footer']['service_links'] as $index => $service) {
            FooterMenuItem::query()->updateOrCreate(
                ['group_key' => 'services', 'label' => $service],
                [
                    'group_title' => 'Services',
                    'route_name' => null,
                    'url' => null,
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ]
            );
        }
    }


    /** @param array<string,mixed> $content */
    private function seedSocialLinks(array $content): void
    {
        foreach ($content['social_links'] as $index => $social) {
            SocialLink::query()->updateOrCreate(
                ['platform' => $social['platform']],
                [
                    'label' => $social['label'],
                    'url' => $social['url'] ?? '#',
                    'icon_svg' => null,
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ]
            );
        }
    }

    /** @param array<string,mixed> $content */
    private function seedHomeSections(array $content): void
    {
        $home = $content['pages']['home'];

        $hero = $this->homeSection('home_hero', 'Hero Section', 10, [
            'label' => $home['hero']['label'],
            'title' => $home['hero']['title'],
            'description' => $home['hero']['description'],
            'settings' => ['social_label' => $home['hero']['social_label']],
        ]);
        $this->syncHomeItems($hero, array_merge(
            array_map(fn ($chip, $index) => ['item_type' => 'chip', 'title' => $chip, 'sort_order' => ($index + 1) * 10], $home['hero']['chips'], array_keys($home['hero']['chips'])),
            array_map(fn ($button, $index) => ['item_type' => 'button', 'button_text' => $button['text'], 'button_route' => $button['route'] ?? null, 'button_url' => $button['url'] ?? null, 'button_class' => $button['class'] ?? null, 'sort_order' => 100 + (($index + 1) * 10)], $home['hero']['buttons'], array_keys($home['hero']['buttons'])),
            array_map(fn ($ticker, $index) => ['item_type' => 'ticker', 'text' => $ticker, 'sort_order' => 200 + (($index + 1) * 10)], $home['hero']['ticker'], array_keys($home['hero']['ticker']))
        ));

        $founder = $this->homeSection('home_founder', "Founder's Message Section", 20, [
            'label' => $home['founder']['label'],
            'title' => $home['founder']['title'],
            'description' => implode("\n\n", $home['founder']['paragraphs']),
            'button_text' => $home['founder']['button']['text'] ?? 'View my digital resume',
            'button_route' => $home['founder']['button']['route'] ?? 'starpmaminul.portfolio',
            'button_url' => $home['founder']['button']['url'] ?? null,
            'settings' => ['name' => $home['founder']['name'], 'role' => $home['founder']['role']],
        ]);
        $founder->items()->where('item_type', 'paragraph')->delete();

        $who = $this->homeSection('home_who', 'Why ITQAN Exists Section', 30, [
            'label' => $home['who']['label'],
            'title' => $home['who']['title'],
            'lead' => $home['who']['lead'],
        ]);
        $this->syncHomeItems($who, array_map(fn ($card, $index) => [
            'item_type' => 'card',
            'badge' => null,
            'title' => $card['title'],
            'text' => $card['text'],
            'settings' => [
                'response' => $card['response'] ?? '',
                'stage_label' => $card['stage_label'] ?? '',
            ],
            'sort_order' => ($index + 1) * 10,
        ], $home['who']['cards'], array_keys($home['who']['cards'])));

        $problems = $this->homeSection('home_problems', 'Interactive Clarity Check Section', 40, [
            'label' => $home['problems']['label'],
            'title' => $home['problems']['title'],
            'lead' => $home['problems']['lead'],
        ]);
        $this->syncHomeItems($problems, array_map(fn ($item, $index) => [
            'item_type' => 'problem',
            'title' => $item['problem'],
            'text' => $item['summary'],
            'settings' => [
                'problem' => $item['problem'],
                'summary' => $item['summary'],
                'services' => implode("\n", $item['services']),
            ],
            'sort_order' => ($index + 1) * 10,
        ], $home['problems']['items'], array_keys($home['problems']['items'])));

        $services = $this->homeSection('home_services_preview', 'Services Preview Section', 50, [
            'label' => $home['services_preview']['label'],
            'title' => $home['services_preview']['title'],
            'lead' => $home['services_preview']['lead'],
            'button_text' => $home['services_preview']['button']['text'],
            'button_route' => $home['services_preview']['button']['route'],
        ]);
        $this->syncHomeItems($services, array_map(fn ($item, $index) => [
            'item_type' => 'service_card',
            'badge' => $item['num'],
            'title' => $item['title'],
            'text' => $item['text'],
            'settings' => [
                'common_problem' => $item['common_problem'] ?? '',
                'deliverables' => $item['deliverables'] ?? '',
            ],
            'sort_order' => ($index + 1) * 10,
        ], $home['services_preview']['items'], array_keys($home['services_preview']['items'])));

        $working = $this->homeSection('home_working', 'Our Way of Working Section', 60, [
            'label' => $home['working']['label'],
            'title' => $home['working']['title'],
            'lead' => $home['working']['intro'],
        ]);
        $this->syncHomeItems($working, array_map(fn ($item, $index) => ['item_type' => 'step', 'badge' => $item['num'], 'title' => $item['title'], 'text' => $item['text'], 'sort_order' => ($index + 1) * 10], $home['working']['items'], array_keys($home['working']['items'])));

        $testimonials = $this->homeSection('home_testimonials', 'Client Words Section', 70, [
            'label' => $home['testimonials']['label'],
            'title' => $home['testimonials']['title'],
            'lead' => $home['testimonials']['lead'] ?? null,
        ]);
        $this->syncHomeItems($testimonials, array_map(fn ($item, $index) => [
            'item_type' => 'testimonial',
            'title' => $item['title'],
            'text' => $item['text'],
            'settings' => [
                'author' => $item['author'],
                'role' => $item['role'],
                'project' => $item['project'] ?? '',
            ],
            'sort_order' => ($index + 1) * 10,
        ], $content['collections']['testimonials'], array_keys($content['collections']['testimonials'])));

        $this->homeSection('home_works_preview', 'Works Preview Section', 80, [
            'label' => $home['works_preview']['label'],
            'title' => $home['works_preview']['title'],
            'button_text' => $home['works_preview']['button']['text'],
            'button_route' => $home['works_preview']['button']['route'],
        ]);

        $values = $this->homeSection('home_values', 'How We Think Section', 90, [
            'label' => $home['values']['label'],
            'title' => $home['values']['title'],
            'lead' => $home['values']['lead'] ?? null,
        ]);
        $this->syncHomeItems($values, array_map(fn ($item, $index) => [
            'item_type' => 'value',
            'badge' => $item['num'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'title' => $item['title'],
            'text' => $item['text'],
            'settings' => [
                'mini' => $item['mini'] ?? '',
                'example' => $item['example'] ?? '',
            ],
            'sort_order' => ($index + 1) * 10,
        ], $home['values']['items'], array_keys($home['values']['items'])));

        $this->homeSection('home_cta', 'Digital Contact Card Section', 100, [
            'label' => $home['cta']['eyebrow'],
            'title' => $home['cta']['title'],
            'lead' => $home['cta']['text'],
            'button_text' => $home['cta']['button']['text'],
            'button_route' => $home['cta']['button']['route'],
            'button_url' => $home['cta']['button']['url'],
            'settings' => [
                'save_button_text' => $home['cta']['save_button_text'],
                'qr_alt' => $home['cta']['qr_alt'],
                'qr_caption' => $home['cta']['qr_caption'],
                'contact_file_name' => $home['cta']['contact_file_name'],
                'first_name' => $home['cta']['vcard']['first_name'],
                'last_name' => $home['cta']['vcard']['last_name'],
                'full_name' => $home['cta']['vcard']['full_name'],
                'credentials' => $home['cta']['vcard']['credentials'],
                'organization' => $home['cta']['vcard']['organization'],
                'job_title' => $home['cta']['vcard']['job_title'],
                'phone' => $home['cta']['vcard']['phone'],
                'whatsapp' => $home['cta']['vcard']['whatsapp'],
                'email' => $home['cta']['vcard']['email'],
                'website' => $home['cta']['vcard']['website'],
                'note' => $home['cta']['vcard']['note'],
            ],
        ]);
    }

    /** @param array<string,mixed> $content */
    private function seedPageSections(array $content): void
    {
        $this->seedAboutPage($content);
        $this->seedServicesPage($content);
        $this->seedWorksPage($content);
        $this->seedCatalogPage($content);
        $this->seedContactPage($content);
    }

    /** @param array<string,mixed> $content */
    private function seedAboutPage(array $content): void
    {
        $about = $content['pages']['about'];

        $hero = $this->pageSection('about', 'about_hero', 'Hero Section', 10, $this->heroData($about['hero']));
        $this->syncPageItems($hero, $this->buttonItems($about['hero']['buttons'] ?? []));

        $this->pageSection('about', 'about_story', 'Our Story Section', 20, [
            'label' => $about['story']['label'],
            'title' => $about['story']['title'],
            'description' => implode("\n\n", $about['story']['paragraphs']),
        ]);

        $beliefs = $this->pageSection('about', 'about_beliefs', 'What We Believe Section', 30, [
            'label' => $about['beliefs']['label'],
            'title' => $about['beliefs']['title'],
        ]);
        $this->syncPageItems($beliefs, array_map(fn ($item, $index) => ['item_type' => 'card', 'title' => $item['title'], 'text' => $item['text'], 'sort_order' => ($index + 1) * 10], $about['beliefs']['items'], array_keys($about['beliefs']['items'])));

        $mission = $this->pageSection('about', 'about_mission_vision', 'Mission & Vision Section', 40, []);
        $this->syncPageItems($mission, array_map(fn ($item, $index) => ['item_type' => 'mission_card', 'badge' => $item['num'], 'title' => $item['title'], 'text' => $item['text'], 'sort_order' => ($index + 1) * 10], $about['mission_vision'], array_keys($about['mission_vision'])));

        $this->pageSection('about', 'about_cta', 'CTA Section', 50, $this->ctaSectionData($about['cta']));
    }

    /** @param array<string,mixed> $content */
    private function seedServicesPage(array $content): void
    {
        $services = $content['pages']['services'];

        $hero = $this->pageSection('services', 'services_hero', 'Hero Section', 10, $this->heroData($services['hero']));
        $this->syncPageItems($hero, $this->buttonItems($services['hero']['buttons'] ?? []));

        $areas = $this->pageSection('services', 'services_areas', 'Service Areas Section', 20, []);
        $this->syncPageItems($areas, array_map(fn ($item, $index) => ['item_type' => 'service_area', 'title' => $item['title'], 'subtitle' => $item['intro'], 'button_text' => $item['button'], 'button_route' => $item['button_route'] ?? 'contact', 'button_url' => $item['button_url'] ?? null, 'settings' => ['points' => implode("\n", $item['points'])], 'sort_order' => ($index + 1) * 10], $content['collections']['services'], array_keys($content['collections']['services'])));

        $faq = $this->pageSection('services', 'services_faq', 'Common Questions Section', 30, [
            'title' => $services['faq_title'],
        ]);
        $this->syncPageItems($faq, array_map(fn ($item, $index) => ['item_type' => 'faq', 'title' => $item['question'], 'subtitle' => $item['summary'], 'text' => implode("\n\n", $item['answer']), 'sort_order' => ($index + 1) * 10], $content['collections']['service_faqs'], array_keys($content['collections']['service_faqs'])));

        $this->pageSection('services', 'services_cta', 'CTA Section', 40, $this->ctaSectionData($services['cta']));
    }

    /** @param array<string,mixed> $content */
    private function seedWorksPage(array $content): void
    {
        $works = $content['pages']['works'];

        $hero = $this->pageSection('works', 'works_hero', 'Hero Section', 10, $this->heroData($works['hero']));
        $this->syncPageItems($hero, $this->buttonItems($works['hero']['buttons'] ?? []));

        $grid = $this->pageSection('works', 'works_grid', 'Work Listing Section', 20, []);
        $filterItems = array_map(fn ($item, $index) => ['item_type' => 'filter', 'title' => $item['label'], 'settings' => ['value' => $item['value']], 'sort_order' => ($index + 1) * 10], $content['collections']['work_filters'], array_keys($content['collections']['work_filters']));
        $workItems = array_map(fn ($item, $index) => ['item_type' => 'work', 'badge' => $item['pill'], 'title' => $item['title'], 'text' => $item['description'], 'button_text' => $item['button_text'] ?? 'View Case Study', 'button_route' => $item['button_route'] ?? null, 'button_url' => $item['button_url'] ?? null, 'settings' => ['categories' => implode(',', $item['categories']), 'tags' => implode(',', $item['tags']), 'featured_on_home' => $index < 4 ? '1' : '0'], 'sort_order' => 100 + (($index + 1) * 10)], $content['collections']['works'], array_keys($content['collections']['works']));
        $this->syncPageItems($grid, array_merge($filterItems, $workItems));
    }

    /** @param array<string,mixed> $content */
    private function seedCatalogPage(array $content): void
    {
        $catalog = $content['pages']['catalog'];

        $hero = $this->pageSection('catalog', 'catalog_hero', 'Hero Section', 10, $this->heroData($catalog['hero']));
        $this->syncPageItems($hero, $this->buttonItems($catalog['hero']['buttons'] ?? []));

        $viewer = $this->pageSection('catalog', 'catalog_viewer', 'Catalog Viewer Section', 20, [
            'settings' => ['viewer_eyebrow' => 'Catalog Viewer', 'viewer_title' => 'ITQAN Service Profile'],
        ]);
        $this->syncPageItems($viewer, array_map(fn ($item, $index) => ['item_type' => 'catalog_page', 'title' => $item['title'], 'subtitle' => $item['kicker'], 'text' => $item['body'], 'settings' => ['type' => $item['type']], 'sort_order' => ($index + 1) * 10], $content['collections']['catalog_pages'], array_keys($content['collections']['catalog_pages'])));
    }

    /** @param array<string,mixed> $content */
    private function seedContactPage(array $content): void
    {
        $contact = $content['pages']['contact'];
        $formContent = $contact['form'];
        $formSteps = $formContent['steps'] ?? [];

        $this->pageSection('contact', 'contact_hero', 'Hero Section', 10, $this->heroData($contact['hero']));

        $this->pageSection('contact', 'contact_form', 'Interactive Contact Form', 20, [
            'label' => $formContent['label'],
            'title' => $formContent['title'],
            'lead' => $formContent['intro'],
            'settings' => [
                'problems' => implode("\n", $content['collections']['contact_options']['problems']),
                'needs' => implode("\n", $content['collections']['contact_options']['needs']),
                'methods' => implode("\n", $content['collections']['contact_options']['methods']),
                'budgets' => implode("\n", $content['collections']['contact_options']['budgets']),
                'problem_step_title' => $formSteps[0]['title'] ?? '',
                'problem_step_text' => $formSteps[0]['text'] ?? '',
                'support_step_title' => $formSteps[1]['title'] ?? '',
                'support_step_text' => $formSteps[1]['text'] ?? '',
                'details_step_title' => $formSteps[2]['title'] ?? '',
                'details_step_text' => $formSteps[2]['text'] ?? '',
                'message_step_title' => $formSteps[3]['title'] ?? '',
                'message_step_text' => $formSteps[3]['text'] ?? '',
                'submit_text' => $formContent['submit_text'],
                'success_title' => $formContent['success_title'],
                'success_text' => $formContent['success_text'],
            ],
        ]);

        $this->pageSection('contact', 'contact_cta', 'QR Contact Card', 30, [
            'label' => $contact['cta']['eyebrow'],
            'title' => $contact['cta']['title'],
            'lead' => $contact['cta']['text'],
            'button_text' => $contact['cta']['button']['text'],
            'button_route' => $contact['cta']['button']['route'],
            'button_url' => $contact['cta']['button']['url'],
            'settings' => [
                'save_button_text' => $contact['cta']['save_button_text'],
                'qr_alt' => $contact['cta']['qr_alt'],
                'qr_caption' => $contact['cta']['qr_caption'],
                'contact_file_name' => $contact['cta']['contact_file_name'],
                'first_name' => $contact['cta']['vcard']['first_name'],
                'last_name' => $contact['cta']['vcard']['last_name'],
                'full_name' => $contact['cta']['vcard']['full_name'],
                'credentials' => $contact['cta']['vcard']['credentials'],
                'organization' => $contact['cta']['vcard']['organization'],
                'job_title' => $contact['cta']['vcard']['job_title'],
                'phone' => $contact['cta']['vcard']['phone'],
                'whatsapp' => $contact['cta']['vcard']['whatsapp'],
                'email' => $contact['cta']['vcard']['email'],
                'website' => $contact['cta']['vcard']['website'],
                'note' => $contact['cta']['vcard']['note'],
            ],
        ]);
    }

    /** @param array<string,mixed> $hero */
    private function heroData(array $hero): array
    {
        return [
            'label' => $hero['label'],
            'title' => $hero['title'],
            'description' => $hero['description'],
        ];
    }

    /** @param array<string,mixed> $cta */
    private function ctaSectionData(array $cta): array
    {
        return [
            'title' => $cta['title'],
            'lead' => $cta['text'],
            'button_text' => $cta['button']['text'] ?? null,
            'button_route' => $cta['button']['route'] ?? null,
            'button_url' => $cta['button']['url'] ?? null,
        ];
    }

    /** @param array<int,array<string,mixed>> $buttons */
    private function buttonItems(array $buttons): array
    {
        return array_map(fn ($button, $index) => [
            'item_type' => 'button',
            'button_text' => $button['text'],
            'button_route' => $button['route'] ?? null,
            'button_url' => $button['url'] ?? null,
            'button_class' => $button['class'] ?? null,
            'sort_order' => ($index + 1) * 10,
        ], $buttons, array_keys($buttons));
    }

    /** @param array<string,mixed> $data */
    private function homeSection(string $key, string $adminTitle, int $sortOrder, array $data): HomeSection
    {
        return HomeSection::query()->updateOrCreate(
            ['section_key' => $key],
            array_merge($data, [
                'admin_title' => $adminTitle,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ])
        );
    }

    /** @param array<string,mixed> $data */
    private function pageSection(string $pageKey, string $key, string $adminTitle, int $sortOrder, array $data): PageSection
    {
        return PageSection::query()->updateOrCreate(
            ['section_key' => $key],
            array_merge($data, [
                'page_key' => $pageKey,
                'admin_title' => $adminTitle,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ])
        );
    }

    /** @param array<int,array<string,mixed>> $items */
    private function syncHomeItems(HomeSection $section, array $items): void
    {
        if ($section->items()->exists()) {
            return;
        }

        foreach ($items as $item) {
            $section->items()->create(array_merge(['is_active' => true], $item));
        }
    }

    /** @param array<int,array<string,mixed>> $items */
    private function syncPageItems(PageSection $section, array $items): void
    {
        if ($section->items()->exists()) {
            return;
        }

        foreach ($items as $item) {
            $section->items()->create(array_merge(['is_active' => true], $item));
        }
    }
}
