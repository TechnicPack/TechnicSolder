<?php

class Solder_Controller extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->filter('before', 'auth');
	}

	public function action_configure()
	{
		if (Input::get('edit-solder'))
		{
			Config::set('solder.mirror_url',Input::get('mirror_url'));
			Config::set('solder.repo_location',Input::get('repo_location'));
			Config::set('solder.platform_key',Input::get('platform_key'));
			return Redirect::to('solder/configure')
				->with('success','Your solder configuration has been updated.');
		}
		return View::make('solder.configure');
	}

}