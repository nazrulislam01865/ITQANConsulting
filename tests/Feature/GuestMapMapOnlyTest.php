<?php

namespace Tests\Feature;

use Database\Seeders\ItqanExternalGuestMapSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestMapMapOnlyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItqanExternalGuestMapSeeder::class);
    }

    public function test_guest_map_and_saved_path_api_are_available(): void
    {
        $this->get('/external-guest-map')->assertOk()->assertSee('Places &amp; pathway', false);
        $this->getJson('/external-guest-map/api/data')
            ->assertOk()
            ->assertJsonStructure(['map', 'places', 'nodes', 'edges']);
        $this->getJson('/external-guest-map/api/route?from=reception&to=pool&mode=walk')
            ->assertOk()
            ->assertJsonStructure(['from', 'to', 'distance_meters', 'path', 'node_path', 'edge_path']);
    }

    public function test_navigation_logging_endpoints_are_removed(): void
    {
        $this->postJson('/external-guest-map/api/location', [])->assertNotFound();
        $this->postJson('/external-guest-map/api/navigation/finish', [])->assertNotFound();
    }
}
