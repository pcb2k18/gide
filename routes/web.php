<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiographyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Biography Pages ---
// This is the master route that will handle all your biography pages.
// We define the full path explicitly to include `/de/index.php/`.
Route::get('/de/index.php/{slug}', [BiographyController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-]+') // Constrains slug to valid URL characters
    ->name('biography.show');


// --- Homepage Routes ---
// This will handle the German homepage at http://gi-de.test/de
Route::get('/de', function () {
    return 'German Biography Homepage - Coming Soon';
})->name('de.home');

// This will handle the root domain http://gi-de.test and redirect it to the German homepage.
Route::get('/', function () {
    return redirect()->route('de.home');
});

// You can add other static pages for the '/de' section here if needed
// For example:
// Route::get('/de/ueber-uns', [PageController::class, 'about'])->name('de.about');
