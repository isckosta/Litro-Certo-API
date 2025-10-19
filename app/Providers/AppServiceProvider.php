<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register L5-Swagger in non-production environments
        if ($this->app->environment() !== 'production') {
            $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default search radius for geolocation
        config(['app.default_search_radius_km' => env('DEFAULT_SEARCH_RADIUS_KM', 10)]);
        config(['app.max_search_radius_km' => env('MAX_SEARCH_RADIUS_KM', 50)]);
        config(['app.version' => '1.0.0']);
    }
}
