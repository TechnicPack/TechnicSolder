<?php

namespace App\Providers;

use App\Models\Modpack;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

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
            define('SOLDER_STREAM', 'rolling');
        }
        if (! defined('SOLDER_VERSION')) {
            define('SOLDER_VERSION', '0.9.1');
        }

        View::composer('layouts.master', function ($view) {
            $modpacks = Cache::remember('allmodpacks', now()->addMinute(), function () {
                return Modpack::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
            });

            $view->with('allModpacks', $modpacks);
        });

        $this->bootRoute();
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
