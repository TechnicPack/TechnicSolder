<?php

namespace App\Providers;

use App\Libraries\UpdateUtils;
use App\Modpack;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!defined('SOLDER_STREAM')) {
            define('SOLDER_STREAM', 'DEV');
        }
        if (!defined('SOLDER_VERSION')) {
            define('SOLDER_VERSION', 'v0.7.7');
        }

        UpdateUtils::init();

        View::composer('layouts.master', function ($view) {
            $modpacks = Cache::remember('allmodpacks', now()->addMinute(), function () {
                return Modpack::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
            });

            $view->with('allModpacks', $modpacks);
        });
    }
}
