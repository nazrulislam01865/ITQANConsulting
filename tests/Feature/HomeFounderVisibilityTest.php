<?php

namespace Tests\Feature;

use App\Models\HomeSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeFounderVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_founder_section_is_visible_on_homepage(): void
    {
        HomeSection::query()->create([
            'section_key' => 'home_founder',
            'admin_title' => "Founder's Message Section",
            'sort_order' => 20,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="founder-message"', false);
    }

    public function test_disabled_founder_section_is_absent_from_homepage(): void
    {
        HomeSection::query()->create([
            'section_key' => 'home_founder',
            'admin_title' => "Founder's Message Section",
            'sort_order' => 20,
            'is_active' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('id="founder-message"', false);
    }

    public function test_changing_founder_visibility_updates_homepage(): void
    {
        $founder = HomeSection::query()->create([
            'section_key' => 'home_founder',
            'admin_title' => "Founder's Message Section",
            'sort_order' => 20,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertSee('id="founder-message"', false);

        $founder->update(['is_active' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('id="founder-message"', false);
    }
}
