<?php

class Dashboard_Controller extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->filter('before','auth');
	}

	public function action_index()
	{
		return View::make('dashboard.index');
	}
	
}