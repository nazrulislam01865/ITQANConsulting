<?php

namespace App\Services\StarPmAminul;

use App\Models\StarPmAminul\PortfolioSection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PortfolioContentService
{
    private const CACHE_KEY = 'starpmaminul.portfolio.sections';

    /**
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            $definitions = config('starpmaminul.sections', []);
            $stored = Schema::connection('starpmaminul')->hasTable('portfolio_sections')
                ? PortfolioSection::query()->get()->keyBy('section_key')
                : collect();

            return collect($definitions)
                ->mapWithKeys(function (array $definition, string $key) use ($stored): array {
                    $saved = (array) ($stored->get($key)?->data ?? []);

                    return [
                        $key => $this->mergeWithDefaults(
                            (array) ($definition['defaults'] ?? []),
                            $saved,
                        ),
                    ];
                })
                ->all();
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function section(string $key): array
    {
        $definitions = config('starpmaminul.sections', []);

        if (! array_key_exists($key, $definitions)) {
            throw (new ModelNotFoundException)->setModel(PortfolioSection::class, [$key]);
        }

        return $this->all()[$key] ?? $definitions[$key]['defaults'];
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(string $key): array
    {
        $definition = config("starpmaminul.sections.{$key}");

        if (! is_array($definition)) {
            throw (new ModelNotFoundException)->setModel(PortfolioSection::class, [$key]);
        }

        return $definition;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(string $key, array $data): PortfolioSection
    {
        $this->definition($key);

        $section = PortfolioSection::query()->updateOrCreate(
            ['section_key' => $key],
            ['data' => $data],
        );

        $this->forgetCache();

        return $section;
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, mixed>
     */
    public function frontendPayload(): array
    {
        $sections = $this->all();
        $projects = collect(Arr::get($sections, 'work.projects', []))
            ->filter(fn (mixed $project): bool => is_array($project))
            ->mapWithKeys(function (array $project, int $index): array {
                $key = trim((string) ($project['key'] ?? ''));

                if ($key === '') {
                    $key = 'project-'.$index;
                }

                return [
                    $key => [
                        'label' => $project['drawer_label'] ?? '',
                        'title' => $project['title'] ?? '',
                        'intro' => $project['drawer_intro'] ?? '',
                        'details' => collect($project['details'] ?? [])
                            ->filter(fn (mixed $detail): bool => is_array($detail))
                            ->map(fn (array $detail): array => [
                                $detail['label'] ?? '',
                                $detail['text'] ?? '',
                            ])->values()->all(),
                    ],
                ];
            })
            ->all();

        return compact('sections', 'projects');
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $saved
     * @return array<string, mixed>
     */
    private function mergeWithDefaults(array $defaults, array $saved): array
    {
        foreach ($saved as $key => $value) {
            if (
                array_key_exists($key, $defaults)
                && is_array($defaults[$key])
                && is_array($value)
                && ! array_is_list($value)
            ) {
                $defaults[$key] = $this->mergeWithDefaults($defaults[$key], $value);
                continue;
            }

            $defaults[$key] = $value;
        }

        return $defaults;
    }
}
