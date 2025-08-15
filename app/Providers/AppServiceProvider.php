<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Make Laravel generate URLs under https://ghanainsider.com/de and HTTPS
        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
            if (str_starts_with(config('app.url'), 'https://')) {
                URL::forceScheme('https');
            }
        }

        // --- Livewire endpoints under /de ---
        // v3 exposes setUpdateRoute() and setFileUploadRoute()
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/de/livewire/update', $handle)->name('livewire.update.de');
        });

        // Some installs don’t have uploads enabled—guard it
        if (method_exists(Livewire::getFacadeRoot(), 'setFileUploadRoute')) {
            Livewire::setFileUploadRoute(function ($handle) {
                return Route::post('/de/livewire/upload-file', $handle)->name('livewire.upload.de');
            });
        }
    }
}
