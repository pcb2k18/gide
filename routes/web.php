<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\SlugRouterController;

// Homepage
Route::get('/', [HomepageController::class, 'index'])->name('home');

// Legacy redirect (/post/{slug} -> /{slug})
Route::get('post/{slug}', fn ($slug) =>
    redirect()->route('biography.show', ['slug' => $slug], 301)
);

// Unified clean slug route (posts + biographies)
// Keep the old name so existing Blade calls still work
Route::get('{slug}', [SlugRouterController::class, 'show'])
    ->where('slug', '^(?!admin|login|register|api|dashboard|sitemap\.xml|robots\.txt|storage|assets|js|css)[A-Za-z0-9\-]+$')
    ->name('biography.show');
