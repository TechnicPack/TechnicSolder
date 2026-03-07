<?php

namespace App\Providers;

use App\Models\Modpack;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
            define('SOLDER_VERSION', '0.12.9');
        }

        Gate::before(fn (User $user) => $user->permission->solder_full ?: null);

        View::composer('layouts.master', function ($view) {
            $modpacks = Cache::remember('allmodpacks', now()->addMinute(), function () {
                return Modpack::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
            });

            $user = Auth::user();
            if ($user) {
                $perms = $user->permission;
                if (! $perms->solder_full) {
                    $modpacks = $modpacks->filter(fn ($modpack) => $perms->canAccessModpack($modpack->id));
                }
            }

            $view->with('allModpacks', $modpacks);
        });

        Password::defaults(fn () => Password::min(8)->uncompromised());

        $this->bootRoute();

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->input('email').'|'.$request->ip()));
        RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));
        RateLimiter::for('key-verify', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
