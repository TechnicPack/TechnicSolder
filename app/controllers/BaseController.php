<?php

class BaseController extends Controller {

	public function __construct()
	{
		define('SOLDER_STREAM', 'DEV');
		if(Cache::has('checker') && Cache::get('checker')){
			if(Cache::has('currentversion')) {
				define('SOLDER_VERSION', Cache::get('currentversion'));
			} else {
				define('SOLDER_VERSION', UpdateUtils::getCurrentVersion());
			}
		} else {
			define('SOLDER_VERSION', 'v0.7.0.9');
		}

	}

	public function showLogin()
	{
		return View::make('dashboard.login');
	}

	public function postLogin()
	{
		$email = Input::get('email');
		$password = Input::get('password');
		$remember = Input::get('remember') ? true : false;

		$credentials = array(
			'email' => $email,
			'password' => $password,
			);

		if ( Auth::attempt($credentials, $remember)) {
		
			Auth::user()->last_ip = Request::ip();
			Auth::user()->save();

			//Check for update on login
			if(!Cache::has('checker')){
				Cache::forever('checker', UpdateUtils::getCheckerEnabled());
			} else {
				if(Cache::get('checker')){
					if(UpdateUtils::getUpdateCheck(true)){
						Cache::put('update', true, 60);
					}
				}
			}

			return Redirect::to('dashboard/');
		} else {
			return Redirect::to('login')->with('login_failed',"Invalid Username/Password");
		}
	}

}