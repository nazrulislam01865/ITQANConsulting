<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExternalGuestMap\Category as MapCategory;
use App\Models\ExternalGuestMap\Edge as MapEdge;
use App\Models\ExternalGuestMap\Node as MapNode;
use App\Models\ExternalGuestMap\Place as MapPlace;
use App\Models\ExternalGuestMap\Setting as MapSetting;
use Database\Seeders\ItqanExternalGuestMapSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MapAdminController extends Controller
{
    public function dashboard()
    {
        $map = $this->activeMap();

        return view('admin.map.dashboard', [
            'map' => $map,
            'placesCount' => $map->places()->count(),
            'nodesCount' => $map->nodes()->count(),
            'edgesCount' => $map->edges()->count(),
            'activePlacesCount' => $map->places()->where('is_active', true)->count(),
            'activeEdgesCount' => $map->edges()->where('is_active', true)->count(),
        ]);
    }

    public function settings()
    {
        return view('admin.map.settings', ['map' => $this->activeMap()]);
    }

    public function updateSettings(Request $request)
    {
        $map = $this->activeMap();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'map_type' => ['required', 'string', 'max:50'],
            'map_file' => ['nullable', 'string', 'max:255'],
            'map_upload' => ['nullable', 'image', 'max:8192'],
            'width' => ['required', 'integer', 'min:100'],
            'height' => ['required', 'integer', 'min:100'],
            'meters_per_pixel' => ['required', 'numeric', 'min:0.0001'],
            'walk_meters_per_minute' => ['required', 'integer', 'min:1'],
            'buggy_meters_per_minute' => ['required', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('map_upload')) {
            $file = $request->file('map_upload');
            $name = 'palace-map-'.time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('assets/itqan-external-guest-map/uploads');
            if (! is_dir($destination)) {
                mkdir($destination, 0775, true);
            }
            $file->move($destination, $name);
            $data['map_file'] = 'assets/itqan-external-guest-map/uploads/'.$name;
        }

        unset($data['map_upload']);
        $map->update($data);

        return back()->with('success', 'Map settings updated.');
    }

    public function places()
    {
        $map = $this->activeMap();

        return view('admin.map.places', [
            'map' => $map,
            'places' => $map->places()->with(['category', 'routeNode'])->orderBy('sort_order')->orderBy('name')->get(),
            'categories' => MapCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'nodes' => $map->nodes()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storePlace(Request $request)
    {
        $map = $this->activeMap();
        $data = $this->validatePlace($request, $map);
        $data['map_setting_id'] = $map->id;
        $data['slug'] = $this->resolveUniqueSlug($map, $data['slug'] ?? null, $data['name']);
        $data['is_qr_point'] = $request->boolean('is_qr_point');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        DB::transaction(fn () => MapPlace::create($data));

        return back()->with('success', 'Place saved to the map database.');
    }

    public function updatePlace(Request $request, MapPlace $place)
    {
        $map = $this->activeMap();
        abort_unless($place->map_setting_id === $map->id, 404);

        $data = $this->validatePlace($request, $map, $place);
        $data['slug'] = $this->resolveUniqueSlug($map, $data['slug'] ?? null, $data['name'], $place->id);
        $data['is_qr_point'] = $request->boolean('is_qr_point');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        DB::transaction(fn () => $place->update($data));

        return back()->with('success', 'Place coordinates and details updated.');
    }

    public function nodes()
    {
        $map = $this->activeMap();

        return view('admin.map.nodes', [
            'map' => $map,
            'nodes' => $map->nodes()->orderBy('code')->get(),
        ]);
    }

    public function storeNode(Request $request)
    {
        $map = $this->activeMap();
        $data = $this->validateNode($request, $map);
        $data['map_setting_id'] = $map->id;
        $data['code'] = $this->resolveUniqueNodeCode($map, $data['code'] ?? null, $data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        DB::transaction(fn () => MapNode::create($data));

        return back()->with('success', 'Route vertex saved.');
    }

    public function updateNode(Request $request, MapNode $node)
    {
        $map = $this->activeMap();
        abort_unless($node->map_setting_id === $map->id, 404);

        $data = $this->validateNode($request, $map, $node);
        $data['code'] = $this->resolveUniqueNodeCode($map, $data['code'] ?? null, $data['name'], $node->id);
        $data['is_active'] = $request->boolean('is_active');

        DB::transaction(function () use ($node, $data) {
            $node->update($data);
            $this->realignConnectedEdges($node->fresh());
        });

        return back()->with('success', 'Route vertex updated and connected path endpoints realigned.');
    }

    public function edges()
    {
        $map = $this->activeMap();

        return view('admin.map.edges', [
            'map' => $map,
            'edges' => $map->edges()->with(['fromNode', 'toNode'])->orderBy('sort_order')->get(),
            'nodes' => $map->nodes()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeEdge(Request $request)
    {
        $map = $this->activeMap();
        $data = $this->validateEdge($request, $map);
        $from = $map->nodes()->findOrFail($data['from_node_id']);
        $to = $map->nodes()->findOrFail($data['to_node_id']);
        $this->assertNoDuplicateEdge($map, $from->id, $to->id);

        $data['map_setting_id'] = $map->id;
        $data['path_points'] = $this->parseAndNormalizePathPoints($request->input('path_points'), $from, $to, $map);
        $data['walk_enabled'] = $request->boolean('walk_enabled', true);
        $data['buggy_enabled'] = $request->boolean('buggy_enabled', true);
        $data['staff_only'] = $request->boolean('staff_only');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['distance_meters'] = $data['distance_meters'] ?: $this->polylineLength($data['path_points']) * (float) $map->meters_per_pixel;

        DB::transaction(fn () => MapEdge::create($data));

        return back()->with('success', 'Curved path connection saved with exact vertex joints.');
    }

    public function updateEdge(Request $request, MapEdge $edge)
    {
        $map = $this->activeMap();
        abort_unless($edge->map_setting_id === $map->id, 404);

        $data = $this->validateEdge($request, $map);
        $from = $map->nodes()->findOrFail($data['from_node_id']);
        $to = $map->nodes()->findOrFail($data['to_node_id']);
        $this->assertNoDuplicateEdge($map, $from->id, $to->id, $edge->id);

        $data['path_points'] = $this->parseAndNormalizePathPoints($request->input('path_points'), $from, $to, $map);
        $data['walk_enabled'] = $request->boolean('walk_enabled');
        $data['buggy_enabled'] = $request->boolean('buggy_enabled');
        $data['staff_only'] = $request->boolean('staff_only');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        if ($request->boolean('recalculate_distance', true) || empty($data['distance_meters'])) {
            $data['distance_meters'] = $this->polylineLength($data['path_points']) * (float) $map->meters_per_pixel;
        }

        DB::transaction(fn () => $edge->update($data));

        return back()->with('success', 'Path connection updated and its endpoints snapped to the selected vertices.');
    }

    public function preview()
    {
        return view('admin.map.preview');
    }

    public function resetSeed()
    {
        Artisan::call('db:seed', ['--class' => ItqanExternalGuestMapSeeder::class, '--force' => true]);

        return redirect()->route('admin.map.dashboard')->with('success', 'Map data reset from the saved seed geometry.');
    }

    private function activeMap(): MapSetting
    {
        return MapSetting::query()->where('is_active', true)->firstOrFail();
    }

    private function validatePlace(Request $request, MapSetting $map, ?MapPlace $place = null): array
    {
        return $request->validate([
            'map_category_id' => ['nullable', Rule::exists('ext_guest_map_categories', 'id')->where('is_active', true)],
            'map_node_id' => ['nullable', Rule::exists('ext_guest_map_nodes', 'id')->where('map_setting_id', $map->id)],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('ext_guest_map_places', 'slug')->where('map_setting_id', $map->id)->ignore($place?->id),
            ],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:4000'],
            'icon' => ['nullable', 'string', 'max:50'],
            'pin_number' => ['nullable', 'integer', 'min:1'],
            'x' => ['required', 'numeric', 'min:0', 'max:'.$map->width],
            'y' => ['required', 'numeric', 'min:0', 'max:'.$map->height],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function validateNode(Request $request, MapSetting $map, ?MapNode $node = null): array
    {
        return $request->validate([
            'code' => [
                'nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('ext_guest_map_nodes', 'code')->where('map_setting_id', $map->id)->ignore($node?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'x' => ['required', 'numeric', 'min:0', 'max:'.$map->width],
            'y' => ['required', 'numeric', 'min:0', 'max:'.$map->height],
            'node_type' => ['required', Rule::in(['junction', 'place', 'entry', 'exit'])],
        ]);
    }

    private function validateEdge(Request $request, MapSetting $map): array
    {
        return $request->validate([
            'from_node_id' => ['required', 'different:to_node_id', Rule::exists('ext_guest_map_nodes', 'id')->where('map_setting_id', $map->id)],
            'to_node_id' => ['required', Rule::exists('ext_guest_map_nodes', 'id')->where('map_setting_id', $map->id)],
            'path_points' => ['nullable', 'string', 'max:100000'],
            'distance_meters' => ['nullable', 'numeric', 'min:0.01'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function parseAndNormalizePathPoints(?string $value, MapNode $from, MapNode $to, MapSetting $map): array
    {
        $fromPoint = ['x' => (float) $from->x, 'y' => (float) $from->y];
        $toPoint = ['x' => (float) $to->x, 'y' => (float) $to->y];

        if ($value === null || trim($value) === '') {
            return $this->gentleCurve($fromPoint, $toPoint, $from->id + $to->id, $map);
        }

        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw ValidationException::withMessages(['path_points' => 'Path points must be valid JSON.']);
        }

        if (! is_array($decoded)) {
            throw ValidationException::withMessages(['path_points' => 'Path points must be a JSON array.']);
        }
        if (count($decoded) > 1000) {
            throw ValidationException::withMessages(['path_points' => 'A path may contain at most 1,000 points.']);
        }

        $points = [];
        foreach ($decoded as $index => $point) {
            if (is_array($point) && array_key_exists('x', $point) && array_key_exists('y', $point)) {
                $x = $point['x'];
                $y = $point['y'];
            } elseif (is_array($point) && array_key_exists(0, $point) && array_key_exists(1, $point)) {
                $x = $point[0];
                $y = $point[1];
            } else {
                throw ValidationException::withMessages(['path_points' => 'Point '.($index + 1).' must contain numeric x and y values.']);
            }

            if (! is_numeric($x) || ! is_numeric($y) || ! is_finite((float) $x) || ! is_finite((float) $y)) {
                throw ValidationException::withMessages(['path_points' => 'Point '.($index + 1).' contains an invalid coordinate.']);
            }
            if ((float) $x < 0 || (float) $x > $map->width || (float) $y < 0 || (float) $y > $map->height) {
                throw ValidationException::withMessages(['path_points' => 'Point '.($index + 1).' lies outside the map boundary.']);
            }

            $candidate = ['x' => round((float) $x, 3), 'y' => round((float) $y, 3)];
            if (! $points || $this->distance(end($points), $candidate) > 0.01) {
                $points[] = $candidate;
            }
        }

        if (count($points) < 2) {
            $points = $this->gentleCurve($fromPoint, $toPoint, $from->id + $to->id, $map);
        } else {
            $points[0] = $fromPoint;
            $points[count($points) - 1] = $toPoint;
            if (count($points) === 2) {
                $points = $this->gentleCurve($fromPoint, $toPoint, $from->id + $to->id, $map);
            }
        }

        return $this->deduplicate($points);
    }

    private function gentleCurve(array $from, array $to, int $seed, MapSetting $map): array
    {
        $dx = $to['x'] - $from['x'];
        $dy = $to['y'] - $from['y'];
        $length = max(1.0, sqrt($dx * $dx + $dy * $dy));
        $sign = $seed % 2 === 0 ? 1 : -1;
        $offset = min(12.0, max(2.0, $length * 0.045)) * $sign;
        $normalX = -$dy / $length;
        $normalY = $dx / $length;

        $clamp = fn (float $value, float $maximum) => round(max(0.0, min($maximum, $value)), 3);

        return [
            $from,
            [
                'x' => $clamp($from['x'] + $dx / 3 + $normalX * $offset, (float) $map->width),
                'y' => $clamp($from['y'] + $dy / 3 + $normalY * $offset, (float) $map->height),
            ],
            [
                'x' => $clamp($from['x'] + 2 * $dx / 3 + $normalX * $offset, (float) $map->width),
                'y' => $clamp($from['y'] + 2 * $dy / 3 + $normalY * $offset, (float) $map->height),
            ],
            $to,
        ];
    }

    private function deduplicate(array $points): array
    {
        $clean = [];
        foreach ($points as $point) {
            if (! $clean || $this->distance(end($clean), $point) > 0.01) {
                $clean[] = ['x' => (float) $point['x'], 'y' => (float) $point['y']];
            }
        }

        return $clean;
    }

    private function assertNoDuplicateEdge(MapSetting $map, int $fromId, int $toId, ?int $ignoreId = null): void
    {
        $query = $map->edges()->where(function ($builder) use ($fromId, $toId) {
            $builder->where(fn ($q) => $q->where('from_node_id', $fromId)->where('to_node_id', $toId))
                ->orWhere(fn ($q) => $q->where('from_node_id', $toId)->where('to_node_id', $fromId));
        });
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        if ($query->exists()) {
            throw ValidationException::withMessages(['from_node_id' => 'A path connection between these two vertices already exists.']);
        }
    }

    private function realignConnectedEdges(MapNode $node): void
    {
        MapEdge::query()
            ->where('map_setting_id', $node->map_setting_id)
            ->where(fn ($query) => $query->where('from_node_id', $node->id)->orWhere('to_node_id', $node->id))
            ->get()
            ->each(function (MapEdge $edge) use ($node) {
                $points = is_array($edge->path_points) ? $edge->path_points : [];
                if (count($points) < 2) {
                    return;
                }
                $point = ['x' => (float) $node->x, 'y' => (float) $node->y];
                if ($edge->from_node_id === $node->id) {
                    $points[0] = $point;
                }
                if ($edge->to_node_id === $node->id) {
                    $points[count($points) - 1] = $point;
                }
                $map = MapSetting::find($node->map_setting_id);
                $normalized = array_values($points);
                $edge->update([
                    'path_points' => $normalized,
                    'distance_meters' => $this->polylineLength($normalized) * (float) ($map?->meters_per_pixel ?? 1),
                ]);
            });
    }

    private function resolveUniqueSlug(MapSetting $map, ?string $requested, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($requested ?: $name, '_') ?: 'place';
        $slug = $base;
        $counter = 2;
        while (MapPlace::query()
            ->where('map_setting_id', $map->id)
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'_'.$counter++;
        }

        return $slug;
    }

    private function resolveUniqueNodeCode(MapSetting $map, ?string $requested, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($requested ?: $name, '_') ?: 'node';
        $code = $base;
        $counter = 2;
        while (MapNode::query()
            ->where('map_setting_id', $map->id)
            ->where('code', $code)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $code = $base.'_'.$counter++;
        }

        return $code;
    }

    private function polylineLength(array $points): float
    {
        $length = 0.0;
        for ($i = 1; $i < count($points); $i++) {
            $length += $this->distance($points[$i - 1], $points[$i]);
        }

        return $length;
    }

    private function distance(array $a, array $b): float
    {
        $dx = (float) $a['x'] - (float) $b['x'];
        $dy = (float) $a['y'] - (float) $b['y'];

        return sqrt($dx * $dx + $dy * $dy);
    }
}
