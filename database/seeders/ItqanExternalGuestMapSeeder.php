<?php

namespace Database\Seeders;

use App\Models\ExternalGuestMap\Category as MapCategory;
use App\Models\ExternalGuestMap\Edge as MapEdge;
use App\Models\ExternalGuestMap\Node as MapNode;
use App\Models\ExternalGuestMap\Place as MapPlace;
use App\Models\ExternalGuestMap\QrPoint as MapQrPoint;
use App\Models\ExternalGuestMap\Setting as MapSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItqanExternalGuestMapSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = public_path('assets/itqan-external-guest-map/seed/palace-path-map-data.json');

        if (! file_exists($jsonPath)) {
            throw new \RuntimeException('Seed data file not found: '.$jsonPath);
        }

        $data = json_decode(file_get_contents($jsonPath), true, 512, JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($data) {
            MapQrPoint::query()->delete();
            MapEdge::query()->delete();
            MapPlace::query()->delete();
            MapNode::query()->delete();
            MapCategory::query()->delete();
            MapSetting::query()->delete();

            $setting = MapSetting::create([
                'name' => 'The Palace Luxury Resort Guest Map',
                'map_type' => 'template_svg',
                'map_file' => null,
                'width' => $data['width'] ?? 2048,
                'height' => $data['height'] ?? 1100,
                'meters_per_pixel' => $data['settings']['metersPerPixel'] ?? 0.82,
                'walk_meters_per_minute' => $data['settings']['walkMetersPerMinute'] ?? 75,
                'buggy_meters_per_minute' => $data['settings']['buggyMetersPerMinute'] ?? 180,
                'is_active' => true,
            ]);

            $categoryColors = [
                'arrival' => '#2563eb',
                'stay' => '#db3eb1',
                'food' => '#f97316',
                'activity' => '#16a34a',
                'service' => '#64748b',
                'emergency' => '#dc2626',
            ];

            $categories = [];
            foreach ($data['categories'] ?? [] as $index => $cat) {
                if (($cat['id'] ?? '') === 'all') {
                    continue;
                }
                $categories[$cat['id']] = MapCategory::create([
                    'name' => $cat['name'],
                    'slug' => $cat['id'],
                    'icon' => null,
                    'color' => $categoryColors[$cat['id']] ?? '#1f6b4b',
                    'sort_order' => $index,
                    'is_active' => true,
                ]);
            }

            $nodes = [];
            foreach ($data['nodes'] ?? [] as $code => $node) {
                $nodes[$code] = MapNode::create([
                    'map_setting_id' => $setting->id,
                    'code' => $code,
                    'name' => $node['label'] ?? Str::headline($code),
                    'x' => $node['x'],
                    'y' => $node['y'],
                    'node_type' => 'junction',
                    'is_active' => true,
                ]);
            }

            foreach ($data['places'] ?? [] as $index => $place) {
                $category = $categories[$place['cat']] ?? null;
                $node = $nodes[$place['routeNode']] ?? null;
                $slug = $place['id'];

                $model = MapPlace::create([
                    'map_setting_id' => $setting->id,
                    'map_category_id' => $category?->id,
                    'map_node_id' => $node?->id,
                    'name' => $place['name'],
                    'slug' => $slug,
                    'subtitle' => Str::headline($place['cat'] ?? 'place'),
                    'description' => $place['desc'] ?? null,
                    'icon' => $place['icon'] ?? $this->iconFor($place['cat'] ?? null),
                    'pin_number' => $place['no'] ?? $index + 1,
                    'x' => $place['x'],
                    'y' => $place['y'],
                    'is_qr_point' => in_array($slug, ['reception', 'parking', 'pool', 'restaurant', 'cafe', 'villas', 'buggy'], true),
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]);

                if ($model->is_qr_point) {
                    MapQrPoint::create([
                        'map_place_id' => $model->id,
                        'title' => $model->name,
                        'qr_code' => $slug,
                        'qr_url' => '/external-guest-map?from='.$slug,
                        'printed_label' => 'Scan for Palace Guest Map - '.$model->name,
                        'is_active' => true,
                    ]);
                }
            }

            foreach ($data['edges'] ?? [] as $index => $edge) {
                $from = $nodes[$edge['a']] ?? null;
                $to = $nodes[$edge['b']] ?? null;

                if (! $from || ! $to) {
                    continue;
                }

                $points = collect($edge['points'] ?? [])
                    ->map(fn ($point) => ['x' => (float) $point[0], 'y' => (float) $point[1]])
                    ->values()
                    ->all();

                MapEdge::create([
                    'map_setting_id' => $setting->id,
                    'from_node_id' => $from->id,
                    'to_node_id' => $to->id,
                    'path_points' => $points,
                    'distance_meters' => isset($edge['distanceMeters']) ? (float) $edge['distanceMeters'] : $this->polylineDistance($points) * (float) $setting->meters_per_pixel,
                    'walk_enabled' => true,
                    'buggy_enabled' => true,
                    'staff_only' => false,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]);
            }
        });
    }

    private function iconFor(?string $category): string
    {
        return match ($category) {
            'arrival' => '🏨',
            'stay' => '🛏️',
            'food' => '🍽️',
            'activity' => '●',
            'service' => '📍',
            'emergency' => '➕',
            default => '📍',
        };
    }

    private function polylineDistance(array $points): float
    {
        $distance = 0.0;
        for ($i = 1; $i < count($points); $i++) {
            $dx = ($points[$i]['x'] ?? 0) - ($points[$i - 1]['x'] ?? 0);
            $dy = ($points[$i]['y'] ?? 0) - ($points[$i - 1]['y'] ?? 0);
            $distance += sqrt($dx * $dx + $dy * $dy);
        }

        return $distance;
    }
}
