<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

// Health status dashboard (spatie/laravel-health) — restricted to signed-in users
Route::get('/health', HealthCheckResultsController::class)->middleware('auth');

// Front-site account pages: profile and password management for any
// authenticated user (Filament admin session shares the same "web" guard).
Route::middleware('auth')->group(function () {
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('cms.account.profile');
    Route::post('/account/profile', [AccountController::class, 'updateProfile'])->name('cms.account.profile.update');
    Route::get('/account/password', [AccountController::class, 'password'])->name('cms.account.password');
    Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('cms.account.password.update');
    Route::post('/logout', [AccountController::class, 'logout'])->name('cms.logout');
});

// CMS pages: the front page renders the "home" page, every other published
// page is served by slug. Keep this catch-all LAST and exclude system paths.
Route::get('/', [PageController::class, 'home'])->name('cms.home');
Route::redirect('/trade-faq', '/faq', 301);
Route::redirect('/request-access', '/service-access', 301);
Route::redirect('/shipping-policy', '/delivery-policy', 301);
Route::redirect('/return-policy', '/service-policy', 301);
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|health|up|storage|livewire|vendor)[a-z0-9\-]+$')
    ->name('cms.page');
