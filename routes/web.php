<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiographyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomepageController;

Route::group(['prefix' => 'de'], function() {
    // --- Homepage Route ---
    // This will handle the main domain: https://ghanainsider.com/de/
    Route::get('/', [HomepageController::class, 'index'])->name('home');

    // --- Guest Post Pages ---
    // This will handle URLs like: https://ghanainsider.com/de/post/guest-post-slug
    Route::get('/post/{slug}', [PostController::class, 'show'])
        ->name('post.show');

    // --- Biography Pages ---
    // This will handle URLs like: https://ghanainsider.com/de/biography-slug
    Route::get('/{slug}', [BiographyController::class, 'show'])
        ->where('slug', '[a-zA-Z0-9\-]+')
        ->name('biography.show');
});
