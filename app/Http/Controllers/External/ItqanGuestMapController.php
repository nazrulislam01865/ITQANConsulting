<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use App\Models\ExternalGuestMap\Edge;
use App\Models\ExternalGuestMap\LocationLog;
use App\Models\ExternalGuestMap\Place;
use App\Models\ExternalGuestMap\RouteLog;
use App\Models\ExternalGuestMap\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItqanGuestMapController extends Controller
{
    public function index(Request $request)
    {
        return view('external.itqan-guest-map.index', [
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ]);
    }

    public function data(): JsonResponse
    {
        $map = $this->activeMap();
        $placeCalibration = $map->places()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get(['slug', 'name', 'x', 'y', 'lat', 'lng'])
            ->map(fn ($place) => [
                'id' => 'place:'.$place->slug,
                'name' => $place->name,
                'x' => (float) $place->x,
                'y' => (float) $place->y,
                'lat' => (float) $place->lat,
                'lng' => (float) $place->lng,
            ]);
        $nodeCalibration = $map->nodes()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get(['code', 'name', 'x', 'y', 'lat', 'lng'])
            ->map(fn ($node) => [
                'id' => 'node:'.$node->code,
                'name' => $node->name,
                'x' => (float) $node->x,
                'y' => (float) $node->y,
                'lat' => (float) $node->lat,
                'lng' => (float) $node->lng,
            ]);
        $calibrationPoints = $placeCalibration->concat($nodeCalibration)->values();
        $trackingMode = $calibrationPoints->count() >= 2
            ? 'absolute_georeferenced'
            : 'anchored_relative';

        return response()->json([
            'map' => [
                'id' => $map->id,
                'name' => $map->name,
                'type' => $map->map_type,
                'image' => $map->map_file ? asset($map->map_file) : null,
                'width' => $map->width,
                'height' => $map->height,
                'meters_per_pixel' => $map->meters_per_pixel,
                'map_north_rotation_deg' => $map->map_north_rotation_deg,
                'walk_meters_per_minute' => $map->walk_meters_per_minute,
                'buggy_meters_per_minute' => $map->buggy_meters_per_minute,
                'tracking_mode' => $trackingMode,
                'georeferenced' => $calibrationPoints->count() >= 2,
                'calibration_points' => $calibrationPoints,
            ],
            'categories' => $map->places()
                ->with('category')
                ->get()
                ->pluck('category')
                ->filter()
                ->unique('id')
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($category) => [
                    'id' => $category->slug,
                    'name' => $category->name,
                    'color' => $category->color,
                    'icon' => $category->icon,
                ]),
            'places' => $map->places()
                ->with(['category', 'routeNode'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($place) => [
                    'id' => $place->slug,
                    'name' => $place->name,
                    'subtitle' => $place->subtitle,
                    'description' => $place->description,
                    'category' => $place->category?->slug,
                    'category_name' => $place->category?->name,
                    'color' => $place->category?->color ?? '#1f6b4b',
                    'icon' => $place->icon,
                    'pin_number' => $place->pin_number,
                    'x' => $place->x,
                    'y' => $place->y,
                    'lat' => $place->lat,
                    'lng' => $place->lng,
                    'route_node_code' => $place->routeNode?->code,
                    'is_qr_point' => $place->is_qr_point,
                ]),
            'nodes' => $map->nodes()
                ->where('is_active', true)
                ->get()
                ->mapWithKeys(fn ($node) => [$node->code => [
                    'id' => $node->id,
                    'name' => $node->name,
                    'x' => $node->x,
                    'y' => $node->y,
                    'lat' => $node->lat,
                    'lng' => $node->lng,
                    'node_type' => $node->node_type,
                ]]),
            'edges' => $map->edges()
                ->with(['fromNode', 'toNode'])
                ->where('is_active', true)
                ->get()
                ->map(fn ($edge) => [
                    'id' => $edge->id,
                    'from' => $edge->fromNode?->code,
                    'to' => $edge->toNode?->code,
                    'path_points' => $edge->path_points ?? [],
                    'distance_meters' => round($this->edgeDistanceMeters($edge, $map), 1),
                    'walk_enabled' => $edge->walk_enabled,
                    'buggy_enabled' => $edge->buggy_enabled,
                    'staff_only' => $edge->staff_only,
                ]),
        ]);
    }

    public function route(Request $request): JsonResponse
    {
        $request->validate([
            'from' => ['required', 'string'],
            'to' => ['required', 'string'],
            'mode' => ['nullable', 'in:walk,buggy'],
        ]);

        $map = $this->activeMap();
        $mode = $request->query('mode', 'walk');
        $fromPlace = Place::query()
            ->where('map_setting_id', $map->id)
            ->where('slug', $request->query('from'))
            ->where('is_active', true)
            ->with('routeNode')
            ->first();
        $toPlace = Place::query()
            ->where('map_setting_id', $map->id)
            ->where('slug', $request->query('to'))
            ->where('is_active', true)
            ->with('routeNode')
            ->first();

        if (! $fromPlace || ! $toPlace) {
            return response()->json(['message' => 'Start or destination place was not found.'], 404);
        }

        if (! $fromPlace->routeNode || ! $toPlace->routeNode) {
            return response()->json(['message' => 'Start or destination is not attached to a route node.'], 422);
        }

        $result = $this->shortestPath($map, $fromPlace->routeNode->id, $toPlace->routeNode->id, $mode);

        if (! $result) {
            return response()->json(['message' => 'No available route for this mode.'], 404);
        }

        $path = $this->withPlaceEndpoints($result['path_points'], $fromPlace, $toPlace);
        $distance = round($result['distance_meters']);
        $walkMinutes = max(1, (int) ceil($distance / max(1, $map->walk_meters_per_minute)));
        $buggyMinutes = max(1, (int) ceil($distance / max(1, $map->buggy_meters_per_minute)));
        $maneuvers = $this->maneuvers($path, $fromPlace->name, $toPlace->name, $map);
        $steps = $this->steps($maneuvers, $walkMinutes);

        $routeLog = RouteLog::create([
            'session_uuid' => (string) Str::uuid(),
            'from_place_id' => $fromPlace->id,
            'to_place_id' => $toPlace->id,
            'from_label' => $fromPlace->name,
            'to_label' => $toPlace->name,
            'start_x' => $fromPlace->x,
            'start_y' => $fromPlace->y,
            'current_x' => $fromPlace->x,
            'current_y' => $fromPlace->y,
            'distance_meters' => $distance,
            'gps_distance_meters' => 0,
            'walk_minutes' => $walkMinutes,
            'buggy_minutes' => $buggyMinutes,
            'route_path' => $path,
            'node_path' => $result['node_codes'],
            'steps' => $steps,
            'mode' => $mode,
            'status' => 'planned',
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return response()->json([
            'route_log_id' => $routeLog->id,
            'session_uuid' => $routeLog->session_uuid,
            'from' => [
                'id' => $fromPlace->slug,
                'name' => $fromPlace->name,
                'x' => $fromPlace->x,
                'y' => $fromPlace->y,
            ],
            'to' => [
                'id' => $toPlace->slug,
                'name' => $toPlace->name,
                'x' => $toPlace->x,
                'y' => $toPlace->y,
            ],
            'mode' => $mode,
            'distance_meters' => $distance,
            'walk_minutes' => $walkMinutes,
            'buggy_minutes' => $buggyMinutes,
            'path' => $path,
            'distance_source' => 'saved_edge_distance_or_map_scale',
            'node_path' => $result['node_codes'],
            'steps' => $steps,
            'maneuvers' => $maneuvers,
            'tracking_mode' => ($map->places()->whereNotNull('lat')->whereNotNull('lng')->count()
                + $map->nodes()->whereNotNull('lat')->whereNotNull('lng')->count()) >= 2
                    ? 'absolute_georeferenced'
                    : 'anchored_relative',
        ]);
    }

    public function trackLocation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'route_log_id' => ['nullable', 'integer', 'exists:ext_guest_map_route_logs,id'],
            'session_uuid' => ['nullable', 'string', 'max:80'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0'],
            'altitude' => ['nullable', 'numeric'],
            'heading' => ['nullable', 'numeric'],
            'speed_meters_per_second' => ['nullable', 'numeric'],
            'map_x' => ['nullable', 'numeric'],
            'map_y' => ['nullable', 'numeric'],
            'gps_distance_meters' => ['nullable', 'numeric', 'min:0'],
            'route_progress_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'source' => ['nullable', 'string', 'max:50'],
        ]);

        $routeLog = null;
        if (! empty($data['route_log_id'])) {
            $routeLog = RouteLog::find($data['route_log_id']);
        }

        $sample = LocationLog::create([
            'map_route_log_id' => $routeLog?->id,
            'session_uuid' => $data['session_uuid'] ?? $routeLog?->session_uuid,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'accuracy_meters' => $data['accuracy_meters'] ?? null,
            'altitude' => $data['altitude'] ?? null,
            'heading' => $data['heading'] ?? null,
            'speed_meters_per_second' => $data['speed_meters_per_second'] ?? null,
            'map_x' => $data['map_x'] ?? null,
            'map_y' => $data['map_y'] ?? null,
            'gps_distance_meters' => $data['gps_distance_meters'] ?? 0,
            'route_progress_percent' => $data['route_progress_percent'] ?? 0,
            'source' => $data['source'] ?? 'browser_geolocation',
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        if ($routeLog) {
            $status = ((float) ($data['route_progress_percent'] ?? 0)) >= 99 ? 'arrived' : 'active';
            $routeLog->update([
                'current_x' => $data['map_x'] ?? $routeLog->current_x,
                'current_y' => $data['map_y'] ?? $routeLog->current_y,
                'start_lat' => $routeLog->start_lat ?? ($data['lat'] ?? null),
                'start_lng' => $routeLog->start_lng ?? ($data['lng'] ?? null),
                'current_lat' => $data['lat'] ?? $routeLog->current_lat,
                'current_lng' => $data['lng'] ?? $routeLog->current_lng,
                'accuracy_meters' => $data['accuracy_meters'] ?? $routeLog->accuracy_meters,
                'gps_distance_meters' => $data['gps_distance_meters'] ?? $routeLog->gps_distance_meters,
                'status' => $status,
                'started_at' => $routeLog->started_at ?? now(),
                'ended_at' => $status === 'arrived' ? now() : $routeLog->ended_at,
                'last_tracked_at' => now(),
            ]);
        }

        return response()->json([
            'ok' => true,
            'sample_id' => $sample->id,
            'route_status' => $routeLog?->fresh()?->status,
        ]);
    }

    public function finishNavigation(Request $request, ?int $routeLog = null): JsonResponse
    {
        if ($routeLog !== null && ! $request->filled('route_log_id')) {
            $request->merge(['route_log_id' => $routeLog]);
        }

        $data = $request->validate([
            'route_log_id' => ['required', 'integer', 'exists:ext_guest_map_route_logs,id'],
            'status' => ['nullable', 'in:stopped,arrived,cancelled'],
        ]);

        $routeLog = RouteLog::findOrFail($data['route_log_id']);
        $routeLog->update([
            'status' => $data['status'] ?? 'stopped',
            'ended_at' => now(),
            'last_tracked_at' => now(),
        ]);

        return response()->json(['ok' => true, 'status' => $routeLog->fresh()->status]);
    }

    private function activeMap(): Setting
    {
        return Setting::query()->where('is_active', true)->firstOrFail();
    }

    private function shortestPath(Setting $map, int $startNodeId, int $endNodeId, string $mode): ?array
    {
        $edges = Edge::query()
            ->where('map_setting_id', $map->id)
            ->where('is_active', true)
            ->where('staff_only', false)
            ->where($mode === 'buggy' ? 'buggy_enabled' : 'walk_enabled', true)
            ->with(['fromNode', 'toNode'])
            ->get();

        $adj = [];
        foreach ($edges as $edge) {
            $a = $edge->from_node_id;
            $b = $edge->to_node_id;
            $w = $this->edgeDistanceMeters($edge, $map);
            $adj[$a][] = ['id' => $b, 'edge' => $edge, 'weight' => $w, 'reverse' => false];
            $adj[$b][] = ['id' => $a, 'edge' => $edge, 'weight' => $w, 'reverse' => true];
        }

        $dist = [$startNodeId => 0.0];
        $prev = [];
        $visited = [];
        $queue = [$startNodeId => 0.0];

        while (! empty($queue)) {
            asort($queue);
            $current = array_key_first($queue);
            unset($queue[$current]);

            if (isset($visited[$current])) {
                continue;
            }

            $visited[$current] = true;

            if ($current === $endNodeId) {
                break;
            }

            foreach ($adj[$current] ?? [] as $neighbor) {
                $next = $neighbor['id'];
                $newDistance = ($dist[$current] ?? INF) + $neighbor['weight'];
                if ($newDistance < ($dist[$next] ?? INF)) {
                    $dist[$next] = $newDistance;
                    $prev[$next] = [
                        'node' => $current,
                        'edge' => $neighbor['edge'],
                        'reverse' => $neighbor['reverse'],
                    ];
                    $queue[$next] = $newDistance;
                }
            }
        }

        if (! isset($dist[$endNodeId])) {
            return null;
        }

        $segments = [];
        $cursor = $endNodeId;
        while ($cursor !== $startNodeId) {
            if (! isset($prev[$cursor])) {
                return null;
            }
            $segments[] = $prev[$cursor];
            $cursor = $prev[$cursor]['node'];
        }

        $segments = array_reverse($segments);
        $pathPoints = [];
        $nodeCodes = [];
        $nodeLabels = [];

        foreach ($segments as $segment) {
            /** @var Edge $edge */
            $edge = $segment['edge'];
            $points = $edge->path_points ?: [
                ['x' => $edge->fromNode->x, 'y' => $edge->fromNode->y],
                ['x' => $edge->toNode->x, 'y' => $edge->toNode->y],
            ];

            if ($segment['reverse']) {
                $points = array_reverse($points);
            }

            foreach ($points as $point) {
                $last = end($pathPoints);
                $current = ['x' => (float) $point['x'], 'y' => (float) $point['y']];
                if ($last && abs($last['x'] - $current['x']) < 0.001 && abs($last['y'] - $current['y']) < 0.001) {
                    continue;
                }
                $pathPoints[] = $current;
            }

            $nodeCodes[] = $edge->fromNode->code;
            $nodeCodes[] = $edge->toNode->code;
            $nodeLabels[] = $edge->fromNode->name;
            $nodeLabels[] = $edge->toNode->name;
        }

        return [
            'distance_meters' => $dist[$endNodeId],
            'path_points' => $pathPoints,
            'node_codes' => array_values(array_unique($nodeCodes)),
            'node_labels' => array_values(array_unique($nodeLabels)),
        ];
    }

    private function withPlaceEndpoints(array $pathPoints, Place $fromPlace, Place $toPlace): array
    {
        $points = $pathPoints;
        $from = ['x' => (float) $fromPlace->x, 'y' => (float) $fromPlace->y];
        $to = ['x' => (float) $toPlace->x, 'y' => (float) $toPlace->y];

        if (empty($points) || $this->pointDistance($from, $points[0]) > 0.001) {
            array_unshift($points, $from);
        }

        $last = end($points);
        if (! $last || $this->pointDistance($to, $last) > 0.001) {
            $points[] = $to;
        }

        return $points;
    }

    private function edgeDistanceMeters(Edge $edge, Setting $map): float
    {
        if ($edge->distance_meters) {
            return (float) $edge->distance_meters;
        }

        $points = $edge->path_points ?: [
            ['x' => $edge->fromNode?->x ?? 0, 'y' => $edge->fromNode?->y ?? 0],
            ['x' => $edge->toNode?->x ?? 0, 'y' => $edge->toNode?->y ?? 0],
        ];
        $distance = 0.0;
        for ($i = 1; $i < count($points); $i++) {
            $distance += $this->pointDistance($points[$i - 1], $points[$i]);
        }

        return $distance * (float) $map->meters_per_pixel;
    }

    private function pointDistance(array $a, array $b): float
    {
        $dx = ((float) ($a['x'] ?? 0)) - ((float) ($b['x'] ?? 0));
        $dy = ((float) ($a['y'] ?? 0)) - ((float) ($b['y'] ?? 0));

        return sqrt($dx * $dx + $dy * $dy);
    }

    private function maneuvers(array $path, string $from, string $to, Setting $map): array
    {
        $points = [];
        foreach ($path as $point) {
            $candidate = ['x' => (float) ($point['x'] ?? 0), 'y' => (float) ($point['y'] ?? 0)];
            $last = end($points);
            if (! $last || $this->pointDistance($last, $candidate) > 0.01) {
                $points[] = $candidate;
            }
        }

        if (count($points) < 2) {
            return [[
                'type' => 'arrive',
                'instruction' => "Arrive at {$to}.",
                'distance_from_start_meters' => 0,
                'distance_to_next_meters' => 0,
                'point' => $points[0] ?? ['x' => 0, 'y' => 0],
                'bearing_after' => null,
            ]];
        }

        $metersPerPixel = max(0.0001, (float) $map->meters_per_pixel);
        $cumulative = [0.0];
        for ($i = 1; $i < count($points); $i++) {
            $cumulative[$i] = $cumulative[$i - 1] + $this->pointDistance($points[$i - 1], $points[$i]) * $metersPerPixel;
        }

        $initialBearing = $this->mapBearing($points[0], $points[1]);
        $maneuvers = [[
            'type' => 'depart',
            'instruction' => 'Head '.$this->bearingLabel($initialBearing).' from '.$from.'.',
            'distance_from_start_meters' => 0,
            'point' => $points[0],
            'bearing_after' => round($initialBearing, 1),
            'turn_degrees' => 0,
        ]];

        $lookAheadMeters = 7.0;
        $minimumTurnDegrees = 28.0;
        $minimumSpacingMeters = 10.0;

        for ($i = 1; $i < count($points) - 1; $i++) {
            $before = $i - 1;
            while ($before > 0 && ($cumulative[$i] - $cumulative[$before]) < $lookAheadMeters) {
                $before--;
            }
            $after = $i + 1;
            while ($after < count($points) - 1 && ($cumulative[$after] - $cumulative[$i]) < $lookAheadMeters) {
                $after++;
            }

            $incoming = $this->mapBearing($points[$before], $points[$i]);
            $outgoing = $this->mapBearing($points[$i], $points[$after]);
            $turn = $this->signedAngle($outgoing - $incoming);
            $absoluteTurn = abs($turn);
            if ($absoluteTurn < $minimumTurnDegrees) {
                continue;
            }

            $type = match (true) {
                $absoluteTurn >= 150 => 'uturn',
                $turn >= 70 => 'turn-right',
                $turn >= $minimumTurnDegrees => 'slight-right',
                $turn <= -70 => 'turn-left',
                default => 'slight-left',
            };
            $instruction = match ($type) {
                'uturn' => 'Make a U-turn.',
                'turn-right' => 'Turn right.',
                'slight-right' => 'Keep slightly right.',
                'turn-left' => 'Turn left.',
                default => 'Keep slightly left.',
            };

            $candidate = [
                'type' => $type,
                'instruction' => $instruction,
                'distance_from_start_meters' => round($cumulative[$i], 1),
                'point' => $points[$i],
                'bearing_after' => round($outgoing, 1),
                'turn_degrees' => round($turn, 1),
            ];

            $lastIndex = count($maneuvers) - 1;
            $lastDistance = (float) ($maneuvers[$lastIndex]['distance_from_start_meters'] ?? 0);
            if ($lastIndex > 0 && $candidate['distance_from_start_meters'] - $lastDistance < $minimumSpacingMeters) {
                if (abs($candidate['turn_degrees']) > abs((float) ($maneuvers[$lastIndex]['turn_degrees'] ?? 0))) {
                    $maneuvers[$lastIndex] = $candidate;
                }
                continue;
            }

            $maneuvers[] = $candidate;
        }

        $totalDistance = end($cumulative) ?: 0;
        $maneuvers[] = [
            'type' => 'arrive',
            'instruction' => "Arrive at {$to}.",
            'distance_from_start_meters' => round($totalDistance, 1),
            'point' => end($points),
            'bearing_after' => null,
            'turn_degrees' => 0,
        ];

        for ($i = 0; $i < count($maneuvers); $i++) {
            $nextDistance = $maneuvers[$i + 1]['distance_from_start_meters'] ?? $maneuvers[$i]['distance_from_start_meters'];
            $maneuvers[$i]['distance_to_next_meters'] = round(max(0, $nextDistance - $maneuvers[$i]['distance_from_start_meters']), 1);
        }

        return $maneuvers;
    }

    private function mapBearing(array $a, array $b): float
    {
        $dx = (float) $b['x'] - (float) $a['x'];
        $dy = (float) $b['y'] - (float) $a['y'];
        $bearing = rad2deg(atan2($dx, -$dy));

        return fmod($bearing + 360.0, 360.0);
    }

    private function signedAngle(float $angle): float
    {
        return fmod($angle + 540.0, 360.0) - 180.0;
    }

    private function bearingLabel(float $bearing): string
    {
        $labels = ['north', 'northeast', 'east', 'southeast', 'south', 'southwest', 'west', 'northwest'];
        $index = ((int) round(fmod($bearing + 360.0, 360.0) / 45.0)) % 8;

        return $labels[$index];
    }

    private function steps(array $maneuvers, int $walkMinutes): array
    {
        $steps = array_values(array_map(
            fn (array $maneuver) => $maneuver['instruction'],
            $maneuvers,
        ));

        if (! empty($steps)) {
            $steps[count($steps) - 1] .= " Estimated walking time: {$walkMinutes} minute(s).";
        }

        return $steps;
    }
}
