<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\Admin\AdminNavigationService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        View::composer('admin.partials.sidebar', function ($view): void {
            $service = app(AdminNavigationService::class);
            $view->with('adminHomeSections', $service->homeSections());
            $view->with('adminPageGroups', $service->pageGroups());
        });

        View::composer('admin.*', function ($view): void {
            $view->with('adminSite', $this->adminSiteData());
        });
    }


    private function configureRateLimiting(): void
    {


        RateLimiter::for('contact-form', function (Request $request) {
            return Limit::perMinutes(5, 5)->by('contact-form:'.$request->ip())
                ->response(function () {
                    return back()
                        ->withInput()
                        ->withErrors(['message' => 'Too many contact messages. Please wait a few minutes and try again.']);
                });
        });

        RateLimiter::for('work-order-form', function (Request $request) {
            return Limit::perMinutes(10, 5)->by('work-order-form:'.$request->ip())
                ->response(function () {
                    return back()
                        ->withInput()
                        ->withErrors(
                            ['project_summary' => 'Too many order requests were submitted. Please wait a few minutes and try again.'],
                            'workOrder'
                        );
                });
        });

        RateLimiter::for('admin-login', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email'));
            $key = 'admin-login:'.$email.'|'.$request->ip();

            return Limit::perMinutes(
                    max(1, (int) config('itqan_security.admin_login_decay_minutes', 1)),
                    max(1, (int) config('itqan_security.admin_login_max_attempts', 5))
                )->by($key)
                ->response(function () {
                    return back()
                        ->withInput(request()->except('password'))
                        ->withErrors(['email' => 'Too many login attempts. Please wait a minute and try again.']);
                });
        });
    }

    /** @return array<string, string|null> */
    private function adminSiteData(): array
    {
        $site = config('itqan.site', []);

        $data = [
            'name' => $site['name'] ?? 'ITQAN Consulting',
            'tagline' => $site['tagline'] ?? 'Sincere Services. Lasting Results.',
            'logo_url' => $site['logo_url'] ?? null,
        ];

        try {
            if (Schema::hasTable('site_settings') && ($settings = SiteSetting::current())) {
                $data['name'] = $settings->site_name ?: $data['name'];
                $data['tagline'] = $settings->tagline ?: $data['tagline'];
                $data['logo_url'] = $settings->logoUrl();
            }
        } catch (Throwable) {
            // Keep admin login/sidebar usable before database setup is complete.
        }

        return $data;
    }
}
