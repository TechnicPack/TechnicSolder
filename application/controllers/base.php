<?php

class Base_Controller extends Controller {

	public function __construct()
	{
		parent::__construct();
		define('SOLDER_STREAM', 'DEV');
		define('SOLDER_VERSION', '0.6');
		Asset::add('jquery', 'js/jquery-1.10.2.js');
		Asset::add('bootstrap-js','js/bootstrap.min.js');
		Asset::add('bootstrap-css', 'css/bootstrap.min.css');
		Asset::add('font-awesome-css', 'font-awesome/css/font-awesome.css');
		Asset::add('sb-admin', 'css/sb-admin.css');
		Asset::add('metis-menu', 'js/plugins/metisMenu/jquery.metisMenu.js');
		Asset::add('sb-admin-js', 'js/sb-admin.js');
		Asset::add('datatables', 'js/plugins/dataTables/jquery.dataTables.js');
		Asset::add('datatables-bs', 'js/plugins/dataTables/dataTables.bootstrap.js');
		Asset::add('datatables-css', 'css/dataTables.bootstrap.css');
		Asset::add('slugify', 'js/jquery.slugify.js');
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