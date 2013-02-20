<?php

class Base_Controller extends Controller {

	public function __construct()
	{
		parent::__construct();
		define('SOLDER_VERSION', '0.1');
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