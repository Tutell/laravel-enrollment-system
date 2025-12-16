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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $branding = \App\Models\BrandingSetting::cached();
            view()->share('branding', $branding);
            if ($branding && $branding->system_name) {
                config(['app.name' => $branding->system_name]);
            }
        } catch (\Throwable $e) {
        }
    }
}
