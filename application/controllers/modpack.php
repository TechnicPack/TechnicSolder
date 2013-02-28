<?php

class Modpack_Controller extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->fi1lter('before','auth');
	}

	public function action_index()
	{
		return View::make('modpack.index');
	}

	public function action_view($modpack_id = null)
	{
		if (empty($modpack_id))
			return Redirect::to('modpack');

		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
			return Redirect::to('modpack');

		return View::make('modpack.view')->with('modpack', $modpack);
	}

	public function action_build($build_id = null)
	{
		if (empty($build_id))
			return Redirect::to('modpack');

		if ($build_id == "create")
			return View::make('modpack.build.create');

		$build = Build::find($build_id);
		if (empty($build))
			return Redirect::to('modpack');

		return View::make('modpack.build.view')->with('build', $build);
	}

	/**
	 * AJAX Methods for Modpack Build Manager
	 **/
	public function action_modify($action = null)
	{
		if (empty($action))
			return Response::error('500');

		switch ($action)
		{
			case "version":
				$sql = 'UPDATE `build_modversion` SET modversion_id=? WHERE id=?';
				$affected = DB::query($sql,array(Input::get('version'), Input::get('pivot_id')));
				return Response::json(array('success' => 'Rows Affected: '.$affected));
				break;
			case "delete":
				$sql = 'DELETE FROM `build_modversion` WHERE id=?';
				$affected = DB::query($sql,array(Input::get('pivot_id')));
				return Response::json(array('success' => 'Rows Affected: '.$affected));
				break;
			case "add":
				$build = Build::find(Input::get('build'));
				$mod = Mod::where('name','=',Input::get('mod-name'))->first();
				$ver = ModVersion::where('mod_id','=', $mod->id)
									->where('version','=', Input::get('mod-version'))
									->first();
				$build->modversions()->attach($ver->id);
				return Response::json(array(
								'pretty_name' => $mod->pretty_name,
								'version' => $ver->version,
								));
		}
	}
}