<?php

namespace App\Providers;

use App\Actions\ResetUserPassword;
use App\Http\Responses\LogoutResponse;
use App\Libraries\UpdateUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->singleton(\Laravel\Fortify\Contracts\ResetsUserPasswords::class, ResetUserPassword::class);
    }

    public function boot(): void
    {
        Fortify::loginView(fn () => view('dashboard.login'));
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('auth.confirm-password'));

        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));

        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                $user->last_ip = $request->ip();
                $user->save();

                if ($user->permission->solder_full && UpdateUtils::getUpdateCheck()) {
                    cache()->put('update', true, now()->addMinutes(60));
                }

                return $user;
            }

            return null;
        });
    }
}
