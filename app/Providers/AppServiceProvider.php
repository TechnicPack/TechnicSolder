<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libraries\UpdateUtils;

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
        if(!defined('SOLDER_STREAM')) {
            define('SOLDER_STREAM', 'DEV');
        }
        if(!defined('SOLDER_VERSION')) {
            define('SOLDER_VERSION', 'v0.7.6');
        }

        UpdateUtils::init();
    }
}
