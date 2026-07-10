<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FooterMenuController;
use App\Http\Controllers\Admin\HeaderMenuController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\Admin\MapAdminController;
use App\Http\Controllers\Admin\HomeSectionItemController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\PageSectionItemController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\ContactSubmissionController as AdminContactSubmissionController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ContactSubmissionController as FrontendContactSubmissionController;
use App\Http\Controllers\External\ItqanGuestMapController;
use App\Http\Controllers\PublicStorageController;
use App\Http\Middleware\EnsureAdminAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/works', [PageController::class, 'works'])->name('works');
Route::get('/catalog', [PageController::class, 'catalog'])->name('catalog');
Route::get('/catalog/download', [PageController::class, 'downloadCatalog'])->name('catalog.download');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [FrontendContactSubmissionController::class, 'store'])->middleware('throttle:contact-form')->name('contact.submit');
Route::get('/storage/{path}', [PublicStorageController::class, 'show'])->where('path', '.*')->name('storage.public');

Route::prefix('external-guest-map')->name('external-guest-map.')->group(function (): void {
    Route::get('/', [ItqanGuestMapController::class, 'index'])->name('index');
    Route::get('/api/data', [ItqanGuestMapController::class, 'data'])->name('api.data');
    Route::get('/api/route', [ItqanGuestMapController::class, 'route'])->name('api.route');
});

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AdminLoginController::class, 'show'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store'])->middleware('throttle:admin-login')->name('login.store');
    Route::get('/session-expired', [AdminLoginController::class, 'expired'])->name('session-expired');

    Route::middleware(EnsureAdminAuthenticated::class)->group(function (): void {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');


        Route::prefix('map')->name('map.')->group(function (): void {
            Route::get('/', [MapAdminController::class, 'dashboard'])->name('dashboard');
            Route::post('/reset-seed', [MapAdminController::class, 'resetSeed'])->name('reset-seed');
            Route::get('/settings', [MapAdminController::class, 'settings'])->name('settings');
            Route::put('/settings', [MapAdminController::class, 'updateSettings'])->name('settings.update');
            Route::get('/places', [MapAdminController::class, 'places'])->name('places');
            Route::post('/places', [MapAdminController::class, 'storePlace'])->name('places.store');
            Route::put('/places/{place}', [MapAdminController::class, 'updatePlace'])->name('places.update');
            Route::get('/nodes', [MapAdminController::class, 'nodes'])->name('nodes');
            Route::post('/nodes', [MapAdminController::class, 'storeNode'])->name('nodes.store');
            Route::put('/nodes/{node}', [MapAdminController::class, 'updateNode'])->name('nodes.update');
            Route::get('/edges', [MapAdminController::class, 'edges'])->name('edges');
            Route::post('/edges', [MapAdminController::class, 'storeEdge'])->name('edges.store');
            Route::put('/edges/{edge}', [MapAdminController::class, 'updateEdge'])->name('edges.update');
            Route::get('/preview', [MapAdminController::class, 'preview'])->name('preview');
        });


        Route::get('/site-settings', [SiteSettingController::class, 'edit'])->name('site-settings.edit');
        Route::put('/site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');

        Route::get('/header-menu', [HeaderMenuController::class, 'index'])->name('header-menu.index');
        Route::post('/header-menu', [HeaderMenuController::class, 'store'])->name('header-menu.store');
        Route::put('/header-menu/{menuItem}', [HeaderMenuController::class, 'update'])->name('header-menu.update');
        Route::delete('/header-menu/{menuItem}', [HeaderMenuController::class, 'destroy'])->name('header-menu.destroy');

        Route::get('/footer-menu', [FooterMenuController::class, 'index'])->name('footer-menu.index');
        Route::post('/footer-menu', [FooterMenuController::class, 'store'])->name('footer-menu.store');
        Route::put('/footer-menu/{menuItem}', [FooterMenuController::class, 'update'])->name('footer-menu.update');
        Route::delete('/footer-menu/{menuItem}', [FooterMenuController::class, 'destroy'])->name('footer-menu.destroy');

        Route::get('/contact-responses', [AdminContactSubmissionController::class, 'index'])->name('contact-submissions.index');
        Route::get('/contact-responses/{contactSubmission}', [AdminContactSubmissionController::class, 'show'])->name('contact-submissions.show');
        Route::delete('/contact-responses/{contactSubmission}', [AdminContactSubmissionController::class, 'destroy'])->name('contact-submissions.destroy');

        Route::get('/social-links', [SocialLinkController::class, 'index'])->name('social-links.index');
        Route::post('/social-links', [SocialLinkController::class, 'store'])->name('social-links.store');
        Route::put('/social-links/{socialLink}', [SocialLinkController::class, 'update'])->name('social-links.update');
        Route::delete('/social-links/{socialLink}', [SocialLinkController::class, 'destroy'])->name('social-links.destroy');

        Route::get('/home', [HomePageController::class, 'index'])->name('home.index');
        Route::get('/home/{section}/edit', [HomePageController::class, 'edit'])->name('home.sections.edit');
        Route::put('/home/{section}', [HomePageController::class, 'update'])->name('home.sections.update');
        Route::post('/home/{section}/items', [HomeSectionItemController::class, 'store'])->name('home.items.store');
        Route::put('/home-items/{item}', [HomeSectionItemController::class, 'update'])->name('home.items.update');
        Route::delete('/home-items/{item}', [HomeSectionItemController::class, 'destroy'])->name('home.items.destroy');

        Route::get('/pages/{pageKey}', [PageContentController::class, 'index'])->name('pages.index');
        Route::get('/pages/{pageKey}/{section}/edit', [PageContentController::class, 'edit'])->name('pages.sections.edit');
        Route::put('/page-sections/{section}', [PageContentController::class, 'update'])->name('pages.sections.update');
        Route::post('/page-sections/{section}/items', [PageSectionItemController::class, 'store'])->name('pages.items.store');
        Route::put('/page-section-items/{item}', [PageSectionItemController::class, 'update'])->name('pages.items.update');
        Route::delete('/page-section-items/{item}', [PageSectionItemController::class, 'destroy'])->name('pages.items.destroy');
    });
});
