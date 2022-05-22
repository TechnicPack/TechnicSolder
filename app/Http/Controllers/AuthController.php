<?php

namespace App\Http\Controllers;

use App\Libraries\UpdateUtils;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('dashboard.login');
    }

    public function postLogin()
    {
        $remember = request()->boolean('remember');

        $credentials = request()->only(['email', 'password']);

        if (auth()->attempt($credentials, $remember)) {
            $user = auth()->user();
            $user->last_ip = request()->ip();
            $user->save();

            // Check for update on login if the user is a superuser
            if ($user->permission->solder_full && UpdateUtils::getUpdateCheck()) {
                cache()->put('update', true, now()->addMinutes(60));
            }

            return redirect()->intended('dashboard');
        } else {
            return redirect()->route('login')->with('login_failed', 'Invalid Username/Password');
        }
    }
}
