<?php

namespace App\Providers;

use App\Support\StorageLinker;
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
        StorageLinker::ensure();

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
