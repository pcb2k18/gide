<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Since the web server points `ghanainsider.com/de` to this application's
| public root, we do not need a '/de' prefix in our routes.
|
*/

// --- Master Content Route ---
// This will now match URLs like: ghanainsider.com/de/index.php/{slug}
Route::get('/index.php/{slug}', [PageController::class, 'resolve'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('page.show');


// --- Homepage Route ---
// This will now match the root of your application, which is ghanainsider.com/de
Route::get('/', function () {
    return 'German Biography Homepage - Coming Soon';
})->name('home');

Route::get('/', [HomepageController::class, 'index'])->name('home'); 
// We no longer need the /de route or the redirect from / to /de.
