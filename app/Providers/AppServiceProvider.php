<?php

namespace App\Providers;

use App\Models\Modpack;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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
        if (! defined('SOLDER_STREAM')) {
            define('SOLDER_STREAM', 'DEV');
        }
        if (! defined('SOLDER_VERSION')) {
            define('SOLDER_VERSION', 'v0.7.16');
        }

        View::composer('layouts.master', function ($view) {
            $modpacks = Cache::remember('allmodpacks', now()->addMinute(), function () {
                return Modpack::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
            });

            $view->with('allModpacks', $modpacks);
        });
    }
}
