<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Statamic\Facades\Icon;

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
        // Heroicons uit resources/svg voor het icoonveld in het control panel (o.a. het blok USP's & cijfers).
        Icon::register('site', resource_path('svg/icons/outline'));
    }
}
