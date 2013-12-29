<?php

class Base_Controller extends Controller {

	public function __construct()
	{
		parent::__construct();
		define('SOLDER_STREAM', 'DEV');
		define('SOLDER_VERSION', '0.5');
	}

	public function action_login()
	{
		return View::make('dashboard.login');
	}

	public function action_do_login()
	{
		$email = Input::get('email');
		$password = Input::get('password');
		$remember = Input::get('remember');

		$credentials = array(
			'username' => $email,
			'password' => $password,
			'remember' => !empty($remember) ? $remember : null
			);
		if ( Auth::attempt($credentials)) {
			Auth::user()->last_ip = Request::ip();
			Auth::user()->save();
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
		return Response::error('404');
	}

}