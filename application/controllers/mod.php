<?php

class Mod_Controller extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->filter('before','auth');
	}

	public function action_list()
	{
		$mods = DB::table('mods')->paginate(20);
		return View::make('mod.list')->with(array('mods' => $mods));
	}
}