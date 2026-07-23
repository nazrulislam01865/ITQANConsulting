<?php

namespace App\Http\Controllers\StarPmAminul\Admin;

use App\Http\Controllers\Controller;
use App\Services\StarPmAminul\PortfolioContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function edit(string $sectionKey, PortfolioContentService $content): View
    {
        return view('starpmaminul.admin.sections.edit', [
            'sectionKey' => $sectionKey,
            'definition' => $content->definition($sectionKey),
            'data' => $content->section($sectionKey),
            'sections' => config('starpmaminul.sections', []),
        ]);
    }

    public function update(Request $request, string $sectionKey, PortfolioContentService $content): RedirectResponse
    {
        $definition = $content->definition($sectionKey);
        $fields = $definition['fields'] ?? [];
        $rules = $this->buildRules($fields);
        $rules['files'] = ['nullable', 'array'];
        $rules['remove_files'] = ['nullable', 'array'];

        foreach ($this->imageFieldPatterns($fields) as $imageField) {
            $rules["files.{$imageField}"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            $rules["remove_files.{$imageField}"] = ['nullable', 'boolean'];
        }

        $validated = Validator::make($request->all(), $rules)->validate();
        $existing = $content->section($sectionKey);
        $existingImages = $this->imagePaths($fields, $existing);
        $allowedExistingImages = array_fill_keys($existingImages, true);

        $submitted = (array) Arr::get($validated, 'data', []);
        $submitted = $this->processImages(
            request: $request,
            fields: $fields,
            data: $submitted,
            allowedExistingImages: $allowedExistingImages,
            directory: "starpmaminul/portfolio/{$sectionKey}",
        );

        $data = $this->normalizeData($fields, $submitted);
        $retainedImages = array_fill_keys($this->imagePaths($fields, $data), true);

        foreach ($existingImages as $oldPath) {
            if (! isset($retainedImages[$oldPath])) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $content->update($sectionKey, $data);

        return redirect()
            ->route('starpmaminul.admin.sections.edit', $sectionKey)
            ->with('status', "{$definition['label']} updated successfully.");
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<string, array<int, mixed>>
     */
    private function buildRules(array $fields, string $prefix = 'data'): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $path = "{$prefix}.{$name}";
            $type = $field['type'] ?? 'text';

            if ($type === 'collection') {
                $rules[$path] = ['nullable', 'array'];
                $rules += $this->buildRules($field['fields'] ?? [], "{$path}.*");
                continue;
            }

            if ($type === 'image') {
                $rules[$path] = ['nullable', 'string', 'max:2048'];
                continue;
            }

            $rules[$path] = match ($type) {
                'email' => ['nullable', 'email', 'max:255'],
                'url' => ['nullable', 'url', 'max:2048'],
                'number' => ['nullable', 'numeric'],
                'textarea' => ['nullable', 'string', 'max:10000'],
                'select' => ['nullable', 'string', 'max:255', 'in:'.implode(',', array_keys($field['options'] ?? []))],
                default => ['nullable', 'string', 'max:2048'],
            };

            if (isset($field['rules']) && is_array($field['rules'])) {
                $rules[$path] = array_merge($rules[$path], $field['rules']);
            }
        }

        return $rules;
    }

    /**
     * Reindex every collection and preserve an explicit empty array when the
     * last item is removed. Nested collections are normalized recursively.
     *
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeData(array $fields, array $data): array
    {
        $normalized = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            if ($type === 'collection') {
                $items = $data[$name] ?? [];

                if (! is_array($items)) {
                    $items = [];
                }

                $normalized[$name] = collect(array_values($items))
                    ->filter(fn (mixed $item): bool => is_array($item))
                    ->map(fn (array $item): array => $this->normalizeData($field['fields'] ?? [], $item))
                    ->values()
                    ->all();

                continue;
            }

            $normalized[$name] = $data[$name] ?? null;
        }

        return $normalized;
    }

    /**
     * Upload, remove and preserve image fields at any collection depth.
     *
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $data
     * @param  array<string, bool>  $allowedExistingImages
     * @return array<string, mixed>
     */
    private function processImages(
        Request $request,
        array $fields,
        array $data,
        array $allowedExistingImages,
        string $directory,
        string $prefix = '',
    ): array {
        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';
            $path = $prefix === '' ? $name : "{$prefix}.{$name}";

            if ($type === 'collection') {
                $items = $data[$name] ?? [];

                if (! is_array($items)) {
                    $data[$name] = [];
                    continue;
                }

                foreach ($items as $index => $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $items[$index] = $this->processImages(
                        request: $request,
                        fields: $field['fields'] ?? [],
                        data: $item,
                        allowedExistingImages: $allowedExistingImages,
                        directory: $directory,
                        prefix: "{$path}.{$index}",
                    );
                }

                $data[$name] = $items;
                continue;
            }

            if ($type !== 'image') {
                continue;
            }

            $currentPath = $data[$name] ?? null;
            $currentPath = is_string($currentPath) && isset($allowedExistingImages[$currentPath])
                ? $currentPath
                : null;

            if ($request->hasFile("files.{$path}")) {
                $data[$name] = $request->file("files.{$path}")->store($directory, 'public');
                continue;
            }

            if ($request->boolean("remove_files.{$path}")) {
                $data[$name] = null;
                continue;
            }

            $data[$name] = $currentPath;
        }

        return $data;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<int, string>
     */
    private function imageFieldPatterns(array $fields, string $prefix = ''): array
    {
        $patterns = [];

        foreach ($fields as $field) {
            $name = $prefix === '' ? $field['name'] : "{$prefix}.{$field['name']}";
            $type = $field['type'] ?? 'text';

            if ($type === 'collection') {
                $patterns = array_merge(
                    $patterns,
                    $this->imageFieldPatterns($field['fields'] ?? [], "{$name}.*"),
                );
                continue;
            }

            if ($type === 'image') {
                $patterns[] = $name;
            }
        }

        return $patterns;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $data
     * @return array<int, string>
     */
    private function imagePaths(array $fields, array $data): array
    {
        $paths = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            if ($type === 'collection') {
                foreach ((array) ($data[$name] ?? []) as $item) {
                    if (is_array($item)) {
                        $paths = array_merge($paths, $this->imagePaths($field['fields'] ?? [], $item));
                    }
                }
                continue;
            }

            if ($type !== 'image') {
                continue;
            }

            $path = $data[$name] ?? null;

            if (is_string($path) && $path !== '') {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }
}
