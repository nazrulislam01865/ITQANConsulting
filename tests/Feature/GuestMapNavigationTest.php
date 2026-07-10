<?php

namespace Tests\Feature;

use App\Models\ExternalGuestMap\RouteLog;
use Database\Seeders\ItqanExternalGuestMapSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestMapNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ItqanExternalGuestMapSeeder::class);
    }

    public function test_guest_map_allows_first_party_mobile_sensor_features(): void
    {
        $this->get('/external-guest-map')
            ->assertOk()
            ->assertHeader(
                'Permissions-Policy',
                'accelerometer=(self), gyroscope=(self), magnetometer=(self), geolocation=(self), screen-wake-lock=(self)'
            );
    }

    public function test_map_data_exposes_navigation_and_calibration_metadata(): void
    {
        $this->getJson('/external-guest-map/api/data')
            ->assertOk()
            ->assertJsonPath('map.tracking_mode', 'anchored_relative')
            ->assertJsonPath('map.georeferenced', false)
            ->assertJsonStructure([
                'map' => ['width', 'height', 'meters_per_pixel', 'calibration_points'],
                'places', 'nodes', 'edges',
            ]);
    }

    public function test_route_returns_geometry_and_turn_maneuvers(): void
    {
        $response = $this->getJson('/external-guest-map/api/route?from=reception&to=pool&mode=walk')
            ->assertOk()
            ->assertJsonStructure([
                'route_log_id', 'session_uuid', 'distance_meters', 'path', 'steps', 'maneuvers',
            ]);

        $this->assertNotEmpty($response->json('path'));
        $this->assertNotEmpty($response->json('maneuvers'));
        $this->assertSame('depart', $response->json('maneuvers.0.type'));
        $this->assertSame('arrive', collect($response->json('maneuvers'))->last()['type']);
    }

    public function test_navigation_can_be_finished_through_the_public_api(): void
    {
        $route = $this->getJson('/external-guest-map/api/route?from=reception&to=pool')->assertOk();
        $routeLogId = $route->json('route_log_id');

        $this->postJson('/external-guest-map/api/navigation/finish', [
            'route_log_id' => $routeLogId,
            'status' => 'stopped',
        ])->assertOk()->assertJsonPath('status', 'stopped');

        $this->assertSame('stopped', RouteLog::findOrFail($routeLogId)->status);
    }
}
