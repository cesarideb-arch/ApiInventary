<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Fix para CGI - asegurar URLs correctas
        if (php_sapi_name() == 'cgi-fcgi') {
            \URL::forceRootUrl(config('app.url'));
        }
    }
}