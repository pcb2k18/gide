<?php

use Illuminate\Support-Facades\Route;
use App\Http\Controllers\BiographyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomepageController;

/*
|--------------------------------------------------------------------------
| Web Routes for Production Server (ghanainsider.com/de)
|--------------------------------------------------------------------------
|
| The web server's DocumentRoot is pointed to the /public directory.
| The `/de` segment is part of the domain/subfolder setup and is not needed in the routes.
|
*/

// --- Homepage Route ---
// This will handle the main domain: https://ghanainsider.com/de/
Route::get('/', [HomepageController::class, 'index'])->name('home');


// --- Biography Pages ---
// This will handle URLs like: https://ghanainsider.com/de/index.php/biography-slug
Route::get('/index.php/{slug}', [BiographyController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('biography.show');


// --- Guest Post Pages ---
// This will handle URLs like: https://ghanainsider.com/de/index.php/post/guest-post-slug
Route::get('/index.php/post/{slug}', [PostController::class, 'show'])
    ->name('post.show');
