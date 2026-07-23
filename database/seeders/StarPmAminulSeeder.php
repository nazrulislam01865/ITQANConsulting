<?php

namespace Database\Seeders;

use App\Models\StarPmAminul\PortfolioSection;
use App\Models\StarPmAminul\User;
use App\Services\StarPmAminul\PortfolioContentService;
use Illuminate\Database\Seeder;

class StarPmAminulSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => config('starpmaminul.admin.email')],
            [
                'name' => config('starpmaminul.admin.name'),
                'password' => config('starpmaminul.admin.password'),
            ],
        );

        foreach (config('starpmaminul.sections', []) as $key => $definition) {
            PortfolioSection::query()->firstOrCreate(
                ['section_key' => $key],
                ['data' => $definition['defaults'] ?? []],
            );
        }

        app(PortfolioContentService::class)->forgetCache();
    }
}
