<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Libraries\UpdateUtils;

class AuthController extends Controller {

    public function showLogin()
    {
        return view('dashboard.login');
    }

    public function postLogin()
    {
        $email = Request::input('email');
        $password = Request::input('password');
        $remember = Request::input('remember') ? true : false;

        $credentials = array(
            'email' => $email,
            'password' => $password,
        );

        if (Auth::attempt($credentials, $remember)) {

            Auth::user()->last_ip = Request::ip();
            Auth::user()->save();

            //Check for update on login
            if (UpdateUtils::getUpdateCheck()) {
                Cache::put('update', true, now()->addMinutes(60));
            }

            return redirect()->intended('dashboard');
        } else {
            return redirect()->route('login')->with('login_failed', "Invalid Username/Password");
        }
    }
}
