<?php

class ModpackController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->beforeFilter('auth');
		$this->beforeFilter('solder_modpacks');
		$this->beforeFilter('modpack', array('only' => array('getView', 'getDelete', 'postDelete', 'getEdit', 'postEdit', 'getAddBuild', 'postAddBuild')));
		$this->beforeFilter('build', array('only' => array('anyBuild')));
	}

	public function getIndex()
	{
		return Redirect::to('modpack/list');
	}

	public function getList()
	{
		$modpacks = Modpack::all();
		return View::make('modpack.list')->with('modpacks', $modpacks);
	}

	public function getView($modpack_id = null)
	{
		if (empty($modpack_id))
			return Redirect::to('modpack');

		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
			return Redirect::to('modpack');

		return View::make('modpack.view')->with('modpack', $modpack);
	}

	public function anyBuild($build_id = null)
	{
		if (empty($build_id))
			return Redirect::to('modpack');

		$build = Build::find($build_id);
		if (empty($build))
			return Redirect::to('modpack');

		if (Input::get('action') == "delete")
		{
			if (Input::get('confirm-delete'))
			{
				$switchrec = 0;
				$switchlat = 0;
				$modpack = $build->modpack;
				if ($build->version == $modpack->recommended)
					$switchrec = 1;
				if ($build->version == $modpack->latest)
					$switchlat = 1;
				$build->modversions()->sync(array());
				$build->delete();
				if ($switchrec)
				{
					$recbuild = Build::where('modpack_id','=',$modpack->id)
										->orderBy('id','desc')->first();
					$modpack->recommended = $recbuild->version;
				}

				if ($switchlat)
				{
					$latbuild = Build::where('modpack_id','=',$modpack->id)
										->orderBy('id','desc')->first();
					$modpack->latest = $latbuild->version;
				}
				$modpack->save();
				Cache::forget('modpack.' . $modpack->slug);
				return Redirect::to('modpack/view/'.$build->modpack->id)->with('deleted','Build deleted.');
			}

			return View::make('modpack.build.delete')->with('build', $build);
		} else
			return View::make('modpack.build.view')->with('build', $build);
	}

	public function getAddBuild($modpack_id)
	{
		if (empty($modpack_id))
			return Redirect::to('modpack');

		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
			return Redirect::to('modpack');

		$minecraft = MinecraftUtils::getMinecraft();

		return View::make('modpack.build.create')
			->with(array(
				'modpack' => $modpack,
				'minecraft' => $minecraft
				));
	}

	public function postAddBuild($modpack_id)
	{
		if (empty($modpack_id))
			return Redirect::to('modpack');

		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
			return Redirect::to('modpack');

		$rules = array(
			"version" => "required",
			);

		$messages = array('version_required' => "You must enter in the build number.");

		$validation = Validator::make(Input::all(), $rules, $messages);
		if ($validation->fails())
			return Redirect::back()->withErrors($validation->messages());

		$clone = Input::get('clone');
		$build = new Build();
		$build->modpack_id = $modpack->id;
		$build->version = Input::get('version');

		$minecraft = explode(':', Input::get('minecraft'));

		$build->minecraft = $minecraft[0];
		$build->minecraft_md5 = $minecraft[1];
		$build->save();
		Cache::forget('modpack.' . $modpack->slug);
		if (!empty($clone))
		{
			$clone_build = Build::find($clone);
			$version_ids = array();
			foreach ($clone_build->modversions as $cver)
			{
				if (!empty($cver))
					array_push($version_ids, $cver->id);
			}
			$build->modversions()->sync($version_ids);
		}

		return Redirect::to('modpack/build/'.$build->id);
	}

	public function getCreate()
	{
		return View::make('modpack.create');
	}

	public function postCreate()
	{

		$rules = array(
			'name' => 'required|unique:modpacks',
			'slug' => 'required|unique:modpacks'
			);

		$messages = array(
			'name_required' => 'You must enter a modpack name.',
			'slug_required' => 'You must enter a modpack slug'
			);

		$validation = Validator::make(Input::all(), $rules, $messages);

		if ($validation->fails())
			return Redirect::back()->withErrors($validation->messages());

		$modpack = new Modpack();
		$modpack->name = Input::get('name');
		$modpack->slug = Str::slug(Input::get('slug'));
		$modpack->save();

		/* Gives creator modpack perms */
		$user = Auth::User();
		$perm = $user->permission;
		$modpacks = $perm->modpacks;
		if(!empty($modpacks)){
			Log::info($modpack->name .': Attempting to add modpack perm to user - '. $user->username . ' - Modpack perm not empty');
			$newmodpacks = array_merge($modpacks, array($modpack->id));
			$perm->modpacks = $newmodpacks;
		}
		else{
			Log::info($modpack->name .': Attempting to add modpack perm to user - '. $user->username . ' - Modpack perm empty');
			$perm->modpacks = array($modpack->id);
		}
		$perm->save();

		return Redirect::to('modpack/view/'.$modpack->id);
	}

	/**
	 * Modpack Edit Interface
	 * @param  Integer $modpack_id Modpack ID
	 * @return View
	 */
	public function getEdit($modpack_id)
	{
		if (empty($modpack_id))
		{
			return Redirect::to('dashboard');
		}

		$modpack = Modpack::find($modpack_id);
		if (empty($modpack_id))
		{
			return Redirect::to('dashboard');
		}

		$clients = array();
		foreach ($modpack->clients as $client) {
			array_push($clients, $client->id);
		}

		return View::make('modpack.edit')->with(array('modpack' => $modpack, 'clients' => $clients));
	}

	public function postEdit($modpack_id)
	{
		if (empty($modpack_id))
		{
			return Redirect::to('dashboard');
		}

		$modpack = Modpack::find($modpack_id);
		if (empty($modpack_id))
		{
			return Redirect::to('dashboard');
		}

		$rules = array(
			'name' => 'required|unique:modpacks,name,'.$modpack->id,
			'slug' => 'required|unique:modpacks,slug,'.$modpack->id
			);

		$messages = array(
			'name_required' => 'You must enter a modpack name.',
			'slug_required' => 'You must enter a modpack slug'
			);

		$validation = Validator::make(Input::all(), $rules, $messages);
		if ($validation->fails())
			return Redirect::back()->withErrors($validation->messages());

		$url = Config::get('solder.repo_location').Input::get('slug').'/resources/';
		$modpack->name = Input::get('name');
		$oldSlug = $modpack->slug;
		$modpack->slug = Input::get('slug');
		$modpack->icon_md5 = UrlUtils::get_remote_md5($url.'icon.png');
		$modpack->logo_md5 = UrlUtils::get_remote_md5($url.'logo_180.png');
		$modpack->background_md5 = UrlUtils::get_remote_md5($url.'background.jpg');
		$modpack->hidden = Input::get('hidden') ? true : false;
		$modpack->private = Input::get('private') ? true : false;
		$modpack->save();

		$useS3 = Config::get('solder.use_s3');

		if ($useS3) {
			$resourcePath = storage_path() . '/resources/' . $modpack->slug;
		} else {
			$resourcePath = public_path() . '/resources/' . $modpack->slug;
		}


		/* Create new resources directory for modpack */
		if (!file_exists($resourcePath)) {
			mkdir($resourcePath, 0775, true);
		}

		/* If slug changed, move resources and delete old slug directory */
		if ($oldSlug != $modpack->slug) {

			$oldPath = public_path() . '/resources/' . $oldSlug;

			if ($useS3) {

				S3::copyObject(Config::get('solder.bucket'), 'resources/'.$oldSlug.'/logo.png', Config::get('solder.bucket'), 'resources/'.$modpack->slug.'/logo.png', S3::ACL_PUBLIC_READ);
				S3::copyObject(Config::get('solder.bucket'), 'resources/'.$oldSlug.'/background.png', Config::get('solder.bucket'), 'resources/'.$modpack->slug.'/background.png', S3::ACL_PUBLIC_READ);
				S3::copyObject(Config::get('solder.bucket'), 'resources/'.$oldSlug.'/icon.png', Config::get('solder.bucket'), 'resources/'.$modpack->slug.'/icon.png', S3::ACL_PUBLIC_READ);
				S3::deleteObject(Config::get('solder.bucket'), 'resources/'.$oldSlug.'/logo.png');
				S3::deleteObject(Config::get('solder.bucket'), 'resources/'.$oldSlug.'/background.png');
				S3::deleteObject(Config::get('solder.bucket'), 'resources/'.$oldSlug.'/icon.png');

				$oldPath = storage_path() . '/resources/' . $oldSlug;
			}

			if (file_exists($oldPath . "/logo.png")) {
				copy($oldPath . "/logo.png", $resourcePath . "/logo.png");
				unlink($oldPath . "/logo.png");
			}

			if (file_exists($oldPath . "/background.png")) {
				copy($oldPath . "/background.png", $resourcePath . "/background.png");
				unlink($oldPath . "/background.png");
			}

			if (file_exists($oldPath . "/icon.png")) {
				copy($oldPath . "/icon.png", $resourcePath . "/icon.png");
				unlink($oldPath . "/icon.png");
			}

			if (file_exists($oldPath)) {
				rmdir($oldPath);
			}
		}

		/* Image dohickery */
		if ($logo = Input::file('logo')) {
			if ($logo->isValid()) {
				$success = Image::make(Input::file('logo'))
			        ->resize(370, 220)->save($resourcePath . '/logo.png', 100);

			    /*
			    if ($useS3) {
			    	S3::putObject(S3::inputFile($resourcePath . '/logo.' . $extension, false), Config::get('solder.bucket'), 'resources/'.$modpack->slug.'/logo.png', S3::ACL_PUBLIC_READ);
			    }
			    */

			    if ($success) {
			        $modpack->logo = true;
			        $modpack->logo_md5 = md5_file($resourcePath . "/logo.png");
			    }
			}
		}

		if ($background = Input::file('background')) {
			if ($background->isValid()) {
				$success = Image::make(Input::file('background'))
			        ->resize(900, 600)->save($resourcePath . '/background.png', 100);

			    /*
			    if ($useS3) {
			    	S3::putObject(S3::inputFile($resourcePath . '/background.' . $extension, false), Config::get('solder.bucket'), 'resources/'.$modpack->slug.'/background.png', S3::ACL_PUBLIC_READ);
			    }
			    */

			    if ($success) {
			        $modpack->background = true;
			        $modpack->background_md5 = md5_file($resourcePath . "/background.png");
			    }
			}
		}

		if ($icon = Input::file('icon')) {
			if ($icon->isValid()) {
				$success = Image::make(Input::file('icon'))
			        ->resize(50, 50)->save($resourcePath . '/icon.png', 100);

				/*
			    if ($useS3) {
			    	S3::putObject(S3::inputFile($resourcePath . '/icon.' . $extension, false), Config::get('solder.bucket'), 'resources/'.$modpack->slug.'/icon.png', S3::ACL_PUBLIC_READ);
			    }
			    */

			    if ($success) {
			        $modpack->icon = true;
			        $modpack->icon_md5 = md5_file($resourcePath . "/icon.png");
			    }
			}
		}

		$modpack->save();

		Cache::forget('modpack.' . $modpack->slug);
		Cache::forget('modpacks');

		/* Client Syncing */
		$clients = Input::get('clients');
		if ($clients){
			$modpack->clients()->sync($clients);
		}
		else{
			$modpack->clients()->sync(array());
		}

		return Redirect::to('modpack/view/'.$modpack->id)->with('success','Modpack edited');
	}

	public function getDelete($modpack_id)
	{
		if (empty($modpack_id))
		{
			return Redirect::to('dashboard');
		}

		$modpack = Modpack::find($modpack_id);
		if (empty($modpack_id))
		{
			return Redirect::to('dashboard');
		}

		return View::make('modpack.delete')->with(array('modpack' => $modpack));
	}

	public function postDelete($modpack_id)
	{
		if (empty($modpack_id))
		{
			return Redirect::to('dashboard');
		}

		$modpack = Modpack::find($modpack_id);
		if (empty($modpack_id))
		{
			return Redirect::to('dashboard');
		}

		foreach ($modpack->builds as $build)
		{
			$build->modversions()->delete();
			$build->delete();
		}

		$modpack->clients()->delete();
		$modpack->delete();
		Cache::forget('modpacks');

		return Redirect::to('modpack/list/')->with('deleted','Modpack Deleted');
	}


	/**
	 * AJAX Methods for Modpack Manager
	 **/
	public function anyModify($action = null)
	{
		if (!Request::ajax())
			return App::abort('404');

		if (empty($action))
			return Response::error('500');

		switch ($action)
		{
			case "version":
				$affected = DB::table('build_modversion')
							->where('build_id','=', Input::get('build_id'))
							->where('modversion_id', '=', Input::get('modversion_id'))
							->update(array('modversion_id' => Input::get('version')));
				return Response::json(array('success' => 'Rows Affected: '.$affected));
				break;
			case "delete":
				$affected = DB::table('build_modversion')
							->where('build_id','=', Input::get('build_id'))
							->where('modversion_id', '=', Input::get('modversion_id'))
							->delete();
				return Response::json(array('success' => 'Rows Affected: '.$affected));
				break;
			case "add":
				$build = Build::find(Input::get('build'));
				$mod = Mod::where('name','=',Input::get('mod-name'))->first();
				$ver = Modversion::where('mod_id','=', $mod->id)
									->where('version','=', Input::get('mod-version'))
									->first();
				$build->modversions()->attach($ver->id);
				return Response::json(array(
								'pretty_name' => $mod->pretty_name,
								'version' => $ver->version,
								));
				break;
			case "recommended":
				$modpack = Modpack::find(Input::get('modpack'));
				$new_version = Input::get('recommended');
				$modpack->recommended = $new_version;
				$modpack->save();

				return Response::json(array(
						"success" => "Updated ".$modpack->name."'s recommended  build to ".$new_version,
						"version" => $new_version
					));
				break;
			case "latest":
				$modpack = Modpack::find(Input::get('modpack'));
				$new_version = Input::get('latest');
				$modpack->latest = $new_version;
				$modpack->save();

				return Response::json(array(
						"success" => "Updated ".$modpack->name."'s latest  build to ".$new_version,
						"version" => $new_version
					));
				break;
			case "published":
				$build = Build::find(Input::get('build'));
				$published = Input::get('published');

				$build->is_published = ($published ? true : false);
				$build->save();

				return Response::json(array(
						"success" => "Updated build ".$build->version."'s published status.",
					));
			case "private":
				$build = Build::find(Input::get('build'));
				$private = Input::get('private');

				$build->private = ($private ? true : false);
				$build->save();

				return Response::json(array(
						"success" => "Updated build ".$build->version."'s private status.",
					));
		}
	}
}
