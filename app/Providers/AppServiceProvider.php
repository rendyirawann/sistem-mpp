<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Paksa root URL (termasuk prefix subfolder /sistem-mpp dari APP_URL) + HTTPS
        // di production. Wajib utk Octane di belakang proxy TLS agar route()/asset()
        // menyertakan prefix subfolder.
        if ($this->app->environment('production') || env('APP_ENV') === 'production') {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }
    }
}
