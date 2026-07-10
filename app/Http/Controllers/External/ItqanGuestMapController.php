<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use App\Models\ExternalGuestMap\Edge;
use App\Models\ExternalGuestMap\Place;
use App\Models\ExternalGuestMap\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $mapImage = $map->map_file ?: 'assets/itqan-external-guest-map/template-map.svg';

        return response()->json([
            'map' => [
                'id' => $map->id,
                'name' => $map->name,
                'type' => $map->map_type,
                'image' => asset($mapImage),
                'width' => (int) $map->width,
                'height' => (int) $map->height,
                'meters_per_pixel' => (float) $map->meters_per_pixel,
                'walk_meters_per_minute' => (int) $map->walk_meters_per_minute,
                'buggy_meters_per_minute' => (int) $map->buggy_meters_per_minute,
            ],
            'categories' => $map->places()
                ->with('category')
                ->where('is_active', true)
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
                ->orderBy('name')
                ->get()
                ->map(fn ($place) => [
                    'id' => $place->slug,
                    'name' => $place->name,
                    'subtitle' => $place->subtitle,
                    'description' => $place->description,
                    'category' => $place->category?->slug,
                    'category_name' => $place->category?->name,
                    'color' => $place->category?->color ?? '#1f6b4b',
                    'icon' => $place->icon ?: '📍',
                    'pin_number' => $place->pin_number,
                    'x' => (float) $place->x,
                    'y' => (float) $place->y,
                    'route_node_code' => $place->routeNode?->code,
                ]),
            'nodes' => $map->nodes()
                ->where('is_active', true)
                ->get()
                ->mapWithKeys(fn ($node) => [$node->code => [
                    'id' => $node->id,
                    'name' => $node->name,
                    'x' => (float) $node->x,
                    'y' => (float) $node->y,
                    'node_type' => $node->node_type,
                ]]),
            'edges' => $map->edges()
                ->with(['fromNode', 'toNode'])
                ->where('is_active', true)
                ->where('staff_only', false)
                ->orderBy('sort_order')
                ->get()
                ->filter(fn ($edge) => $edge->fromNode && $edge->toNode)
                ->map(fn ($edge) => [
                    'id' => $edge->id,
                    'from' => $edge->fromNode->code,
                    'to' => $edge->toNode->code,
                    'path_points' => $this->normalizedEdgePoints($edge),
                    'distance_meters' => round($this->edgeDistanceMeters($edge, $map), 1),
                    'walk_enabled' => (bool) $edge->walk_enabled,
                    'buggy_enabled' => (bool) $edge->buggy_enabled,
                ])
                ->values(),
        ]);
    }

    public function route(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from' => ['required', 'string'],
            'to' => ['required', 'string'],
            'mode' => ['nullable', 'in:walk,buggy'],
        ]);

        $map = $this->activeMap();
        $mode = $data['mode'] ?? 'walk';
        $places = Place::query()
            ->where('map_setting_id', $map->id)
            ->whereIn('slug', [$data['from'], $data['to']])
            ->where('is_active', true)
            ->with('routeNode')
            ->get()
            ->keyBy('slug');

        $fromPlace = $places->get($data['from']);
        $toPlace = $places->get($data['to']);

        if (! $fromPlace || ! $toPlace) {
            return response()->json(['message' => 'Start or destination place was not found.'], 404);
        }

        if (! $fromPlace->routeNode || ! $toPlace->routeNode) {
            return response()->json(['message' => 'Start or destination is not attached to the saved path network.'], 422);
        }

        if ($fromPlace->id === $toPlace->id) {
            return response()->json([
                'from' => $this->placePayload($fromPlace),
                'to' => $this->placePayload($toPlace),
                'mode' => $mode,
                'distance_meters' => 0,
                'walk_minutes' => 0,
                'buggy_minutes' => 0,
                'path' => [['x' => (float) $fromPlace->x, 'y' => (float) $fromPlace->y]],
                'node_path' => [$fromPlace->routeNode->code],
                'edge_path' => [],
            ]);
        }

        $result = $this->shortestPath($map, $fromPlace->routeNode->id, $toPlace->routeNode->id, $mode);
        if (! $result) {
            return response()->json(['message' => 'No saved path connects these places.'], 404);
        }

        $path = $this->withPlaceEndpoints($result['path_points'], $fromPlace, $toPlace);
        $networkPath = $result['path_points'];
        $networkStart = $networkPath[0] ?? $path[0];
        $networkEnd = $networkPath ? $networkPath[count($networkPath) - 1] : $path[count($path) - 1];
        $pathEnd = $path[count($path) - 1];
        $connectorDistance = (
            $this->pointDistance($path[0], $networkStart)
            + $this->pointDistance($networkEnd, $pathEnd)
        ) * max(0.0001, (float) $map->meters_per_pixel);
        $distance = max(0, (int) round($result['distance_meters'] + $connectorDistance));

        return response()->json([
            'from' => $this->placePayload($fromPlace),
            'to' => $this->placePayload($toPlace),
            'mode' => $mode,
            'distance_meters' => $distance,
            'walk_minutes' => $distance === 0 ? 0 : max(1, (int) ceil($distance / max(1, $map->walk_meters_per_minute))),
            'buggy_minutes' => $distance === 0 ? 0 : max(1, (int) ceil($distance / max(1, $map->buggy_meters_per_minute))),
            'path' => $path,
            'node_path' => $result['node_codes'],
            'edge_path' => $result['edge_ids'],
        ]);
    }

    private function activeMap(): Setting
    {
        return Setting::query()->where('is_active', true)->firstOrFail();
    }

    private function placePayload(Place $place): array
    {
        return [
            'id' => $place->slug,
            'name' => $place->name,
            'x' => (float) $place->x,
            'y' => (float) $place->y,
        ];
    }

    private function shortestPath(Setting $map, int $startNodeId, int $endNodeId, string $mode): ?array
    {
        $edges = Edge::query()
            ->where('map_setting_id', $map->id)
            ->where('is_active', true)
            ->where('staff_only', false)
            ->where($mode === 'buggy' ? 'buggy_enabled' : 'walk_enabled', true)
            ->with(['fromNode', 'toNode'])
            ->get()
            ->filter(fn ($edge) => $edge->fromNode && $edge->toNode);

        $adjacency = [];
        foreach ($edges as $edge) {
            $weight = $this->edgeDistanceMeters($edge, $map);
            $adjacency[$edge->from_node_id][] = [
                'node' => $edge->to_node_id,
                'edge' => $edge,
                'reverse' => false,
                'weight' => $weight,
            ];
            $adjacency[$edge->to_node_id][] = [
                'node' => $edge->from_node_id,
                'edge' => $edge,
                'reverse' => true,
                'weight' => $weight,
            ];
        }

        $distances = [$startNodeId => 0.0];
        $previous = [];
        $queue = [$startNodeId => 0.0];
        $visited = [];

        while ($queue) {
            asort($queue, SORT_NUMERIC);
            $current = (int) array_key_first($queue);
            unset($queue[$current]);

            if (isset($visited[$current])) {
                continue;
            }
            $visited[$current] = true;

            if ($current === $endNodeId) {
                break;
            }

            foreach ($adjacency[$current] ?? [] as $candidate) {
                $next = (int) $candidate['node'];
                $newDistance = $distances[$current] + $candidate['weight'];
                if ($newDistance < ($distances[$next] ?? INF)) {
                    $distances[$next] = $newDistance;
                    $previous[$next] = [
                        'previous_node' => $current,
                        'edge' => $candidate['edge'],
                        'reverse' => $candidate['reverse'],
                    ];
                    $queue[$next] = $newDistance;
                }
            }
        }

        if (! array_key_exists($endNodeId, $distances)) {
            return null;
        }

        $segments = [];
        $nodeIds = [$endNodeId];
        $cursor = $endNodeId;
        while ($cursor !== $startNodeId) {
            if (! isset($previous[$cursor])) {
                return null;
            }
            $segments[] = $previous[$cursor];
            $cursor = (int) $previous[$cursor]['previous_node'];
            $nodeIds[] = $cursor;
        }
        $segments = array_reverse($segments);
        $nodeIds = array_reverse($nodeIds);

        $path = [];
        $edgeIds = [];
        foreach ($segments as $segment) {
            /** @var Edge $edge */
            $edge = $segment['edge'];
            $points = $this->normalizedEdgePoints($edge);
            if ($segment['reverse']) {
                $points = array_reverse($points);
            }
            $path = $this->appendPath($path, $points);
            $edgeIds[] = $edge->id;
        }

        $nodesById = $map->nodes()->whereIn('id', $nodeIds)->get()->keyBy('id');

        return [
            'distance_meters' => (float) $distances[$endNodeId],
            'path_points' => $path,
            'node_codes' => array_values(array_filter(array_map(
                fn ($id) => $nodesById->get($id)?->code,
                $nodeIds,
            ))),
            'edge_ids' => $edgeIds,
        ];
    }

    private function normalizedEdgePoints(Edge $edge): array
    {
        $from = ['x' => (float) $edge->fromNode->x, 'y' => (float) $edge->fromNode->y];
        $to = ['x' => (float) $edge->toNode->x, 'y' => (float) $edge->toNode->y];
        $raw = is_array($edge->path_points) ? $edge->path_points : [];
        $points = [];

        foreach ($raw as $point) {
            if (! is_array($point) || ! isset($point['x'], $point['y']) || ! is_numeric($point['x']) || ! is_numeric($point['y'])) {
                continue;
            }
            $candidate = ['x' => (float) $point['x'], 'y' => (float) $point['y']];
            if (! $points || $this->pointDistance(end($points), $candidate) > 0.01) {
                $points[] = $candidate;
            }
        }

        if (count($points) < 2) {
            return [$from, $to];
        }

        $points[0] = $from;
        $points[count($points) - 1] = $to;

        return $this->deduplicatePoints($points);
    }

    private function appendPath(array $base, array $addition): array
    {
        foreach ($addition as $point) {
            $candidate = ['x' => (float) $point['x'], 'y' => (float) $point['y']];
            if (! $base || $this->pointDistance(end($base), $candidate) > 0.01) {
                $base[] = $candidate;
            }
        }

        return $base;
    }

    private function withPlaceEndpoints(array $path, Place $fromPlace, Place $toPlace): array
    {
        $from = ['x' => (float) $fromPlace->x, 'y' => (float) $fromPlace->y];
        $to = ['x' => (float) $toPlace->x, 'y' => (float) $toPlace->y];
        $points = $this->deduplicatePoints($path);

        if (! $points) {
            return $this->deduplicatePoints([$from, $to]);
        }

        if ($this->pointDistance($from, $points[0]) > 0.01) {
            array_unshift($points, $from);
        }
        if ($this->pointDistance($to, end($points)) > 0.01) {
            $points[] = $to;
        }

        return $this->deduplicatePoints($points);
    }

    private function deduplicatePoints(array $points): array
    {
        $clean = [];
        foreach ($points as $point) {
            $candidate = ['x' => (float) $point['x'], 'y' => (float) $point['y']];
            if (! $clean || $this->pointDistance(end($clean), $candidate) > 0.01) {
                $clean[] = $candidate;
            }
        }

        return $clean;
    }

    private function edgeDistanceMeters(Edge $edge, Setting $map): float
    {
        if ($edge->distance_meters !== null && (float) $edge->distance_meters > 0) {
            return (float) $edge->distance_meters;
        }

        $points = $this->normalizedEdgePoints($edge);
        $pixels = 0.0;
        for ($i = 1; $i < count($points); $i++) {
            $pixels += $this->pointDistance($points[$i - 1], $points[$i]);
        }

        return $pixels * max(0.0001, (float) $map->meters_per_pixel);
    }

    private function pointDistance(array $a, array $b): float
    {
        $dx = (float) ($a['x'] ?? 0) - (float) ($b['x'] ?? 0);
        $dy = (float) ($a['y'] ?? 0) - (float) ($b['y'] ?? 0);

        return sqrt($dx * $dx + $dy * $dy);
    }
}
