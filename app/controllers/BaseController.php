<?php

class BaseController extends Controller {

	public function __construct()
	{
		define('SOLDER_STREAM', 'DEV');
		if(UpdateUtils::getCheckerEnabled()){
			define('SOLDER_VERSION', UpdateUtils::getCurrentVersion());
		} else {
			define('SOLDER_VERSION', '0.7.0.8');
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

			try {
				//Check for update on login
				if(UpdateUtils::getCheckerEnabled()){
					Session::put('checker', true);
					if(UpdateUtils::getUpdateCheck(true)){
						Session::put('update', true);
					}
				} else {
					Session::put('checker', false);
				}
			} catch (Exception $e){
				Log::error($exception);
				return Redirect::to('dashboard/');
			}

			return Redirect::to('dashboard/');
		} else {
			return Redirect::to('login')->with('login_failed',"Invalid Username/Password");
		}
	}

	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function __call($method, $parameters)
	{
		return App::abort('404');
	}

}