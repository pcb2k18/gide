<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiographyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomepageController; // <-- Make sure this is imported

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Biography Pages ---
Route::get('/index.php/{slug}', [BiographyController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('biography.show');

// --- Guest Post Pages ---
Route::get('/index.php/post/{slug}', [PostController::class, 'show'])
    ->name('post.show');


// ########## START: CORRECTED HOMEPAGE ROUTE ##########
// This will now handle the homepage: 
Route::get('', [HomepageController::class, 'index'])->name('de.home');
// ########## END: CORRECTED HOMEPAGE ROUTE ##########


// --- Root Domain Redirect ---
// A fallback for the root domain, just in case
Route::get('/', function () {
    return redirect()->route('de.home');
});
