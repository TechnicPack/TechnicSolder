<?php

use Aws\S3\S3Client;
use Illuminate\Support\MessageBag;
class ModpackController extends BaseController {

	public function __construct()
	{
		parent::__construct();
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
		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
			return Redirect::to('modpack/list')->withErrors(new MessageBag(array('Modpack not found')));

		return View::make('modpack.view')->with('modpack', $modpack);
	}

	public function anyBuild($build_id = null)
	{
		$build = Build::find($build_id);
		if (empty($build))
			return Redirect::to('modpack/list')->withErrors(new MessageBag(array('Modpack not found')));

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
		} else if (Input::get('action') == "edit") {
			if (Input::get('confirm-edit'))
			{
				$rules = array(
					"version" => "required",
					"minecraft" => "required",
					"memory" => "numeric"
					);

				$messages = array('version.required' => "You must enter in the build number.",
									'memory.numeric' => "You may enter in numbers only for the memory requirement");

				$validation = Validator::make(Input::all(), $rules, $messages);
				if ($validation->fails())
					return Redirect::to('modpack/build/'.$build->id.'?action=edit')->withErrors($validation->messages());

				$build->version = Input::get('version');

				$minecraft = Input::get('minecraft');

				$build->minecraft = $minecraft;
				$build->min_java = Input::get('java-version');
				$build->min_memory = Input::get('memory-enabled') ? Input::get('memory') : 0;
				$build->save();
				Cache::forget('modpack.' . $build->modpack->slug . '.build.' . $build->version);
				return Redirect::to('modpack/build/'.$build->id);
			}
			$minecraft = MinecraftUtils::getMinecraft();
			return View::make('modpack.build.edit')->with('build', $build)->with('minecraft', $minecraft);
		} else {
			return View::make('modpack.build.view')->with('build', $build);
		}
	}

	public function getAddBuild($modpack_id)
	{
		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
			return Redirect::to('modpack/list')->withErrors(new MessageBag(array('Modpack not found')));

		$minecraft = MinecraftUtils::getMinecraft();

		return View::make('modpack.build.create')
			->with(array(
				'modpack' => $modpack,
				'minecraft' => $minecraft
				));
	}

	public function postAddBuild($modpack_id)
	{
		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
			return Redirect::to('modpack/list')->withErrors(new MessageBag(array('Modpack not found')));

		$rules = array(
					"version" => "required",
					"minecraft" => "required",
					"memory" => "numeric"
					);

		$messages = array('version.required' => "You must enter in the build number.",
							'memory.numeric' => "You may enter in numbers only for the memory requirement");

		$validation = Validator::make(Input::all(), $rules, $messages);
		if ($validation->fails())
			return Redirect::to('modpack/add-build/'.$modpack_id)->withErrors($validation->messages());

		$clone = Input::get('clone');
		$build = new Build();
		$build->modpack_id = $modpack->id;
		$build->version = Input::get('version');

		$minecraft = Input::get('minecraft');

		$build->minecraft = $minecraft;
		$build->min_java = Input::get('java-version');
		$build->min_memory = Input::get('memory-enabled') ? Input::get('memory') : 0;
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
			return Redirect::to('modpack/create')->withErrors($validation->messages());

		$modpack = new Modpack();
		$modpack->name = Input::get('name');
		$modpack->slug = Str::slug(Input::get('slug'));
		$modpack->icon_md5 = md5_file(public_path() . '/resources/default/icon.png');
		$modpack->icon_url = URL::asset('/resources/default/icon.png');
		$modpack->logo_md5 = md5_file(public_path() . '/resources/default/logo.png');
		$modpack->logo_url = URL::asset('/resources/default/logo.png');
		$modpack->background_md5 = md5_file(public_path() . '/resources/default/background.jpg');
		$modpack->background_url = URL::asset('/resources/default/background.jpg');
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

		try {
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
		} catch(Exception $e) {
			Log::error($e);
			return Redirect::to('modpack/create')->withErrors($e->getMessage());
		}

		return Redirect::to('modpack/view/'.$modpack->id);
	}

	/**
	 * Modpack Edit Interface
	 * @param  Integer $modpack_id Modpack ID
	 * @return View
	 */
	public function getEdit($modpack_id)
	{
		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
		{
			return Redirect::to('dashboard')->withErrors(new MessageBag(array('Modpack not found')));
		}

		$clients = array();
		foreach ($modpack->clients as $client) {
			array_push($clients, $client->id);
		}

		$resourcesWritable = is_writable(public_path() . '/resources/' . $modpack->slug);

		return View::make('modpack.edit')->with(array('modpack' => $modpack, 'clients' => $clients, 'resourcesWritable' => $resourcesWritable));
	}

	public function postEdit($modpack_id)
	{
		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
		{
			return Redirect::to('modpack/list/')->withErrors(new MessageBag(array('Modpack not found')));
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
			return Redirect::to('modpack/edit/'.$modpack_id)->withErrors($validation->messages());

		$modpack->name = Input::get('name');
		$oldSlug = $modpack->slug;
		$modpack->slug = Input::get('slug');
		$modpack->hidden = Input::get('hidden') ? true : false;
		$modpack->private = Input::get('private') ? true : false;
		$modpack->save();

		$useS3 = Config::get('solder.use_s3') ? true : false;
		$S3bucket = Config::get('solder.bucket');
		$newSlug = (bool)($oldSlug != $modpack->slug);

		if ($useS3) {
			$resourcePath = storage_path() . '/resources/' . $modpack->slug;
			$oldPath = storage_path() . '/resources/' . $oldSlug;
			$client = S3Client::factory(array(
                        'key' => Config::get('solder.access_key'),
                        'secret' => Config::get('solder.secret_key')
                    ));
			if(!$client->doesBucketExist($S3bucket)) {
				Log::error('Amazon S3 error, Bucket '. $S3bucket . ' does not exist.');
				$useS3 = false;
			}
		}

		if (!$useS3){
			$resourcePath = public_path() . '/resources/' . $modpack->slug;
			$oldPath = public_path() . '/resources/' . $oldSlug;
		}

		/* Create new resources directory for modpack */
		if (!file_exists($resourcePath)) {
			mkdir($resourcePath, 0775, true);
		}

		/* Image dohickery */
		if ($icon = Input::file('icon')) {
			if ($icon->isValid()) {
				$iconimg = Image::make(Input::file('icon')->getRealPath())->resize(50,50)->encode('png', 100);

				if ($success = $iconimg->save($resourcePath . '/icon.png', 100)) {
					$modpack->icon = true;

					if ($useS3) {
						$result = $client->putObject(array(
									'Bucket' => $S3bucket,
									'Key' => '/resources/'.$modpack->slug.'/icon.png',
									'Body' => $iconimg,
									'ACL' => 'public-read',
									'ContentType' => 'image/png'
								));

						$modpack->icon_url = $result['ObjectURL'];
						$modpack->icon_md5 = $result['ETag'];
					} else {
						$modpack->icon_url = URL::asset('/resources/' . $modpack->slug . '/icon.png');
						$modpack->icon_md5 = md5_file($resourcePath . "/icon.png");
					}

					if($newSlug) {
						if ($useS3) {
							$client->deleteObject(array(
								'Bucket' => $S3bucket,
								'Key' => '/resources/'.$modpack->slug.'/icon.png'
							));
						}

						if (file_exists($oldPath . "/icon.png")) {
							unlink($oldPath . "/icon.png");
						}
					}
				} else if (!$success && !$modpack->icon) {
					$modpack->icon_md5 = md5_file(public_path() . '/resources/default/icon.png');
					$modpack->icon_url = URL::asset('/resources/default/icon.png');
					return Redirect::to('modpack/edit/'.$modpack_id)->withErrors(new MessageBag(array('Failed to save new image to ' . $resourcePath . '/icon.png')));
				} else {
					Log::error('Failed to save new image to ' . $resourcePath . '/icon.png');
					return Redirect::to('modpack/edit/'.$modpack_id)->withErrors(new MessageBag(array('Failed to save new image to ' . $resourcePath . '/icon.png')));
				}
			}
		} else {
			if($newSlug) {
				if ($useS3) {
					$client->copyObject(array(
						'Bucket' => $S3bucket,
						'Key' => '/resources/'.$modpack->slug.'/icon.png',
						'CopySource' => '/resources/'.$oldSlug.'/icon.png',
						'ACL' => 'public-read',
						'ContentType' => 'image/png'
					));
					$client->deleteObject(array(
						'Bucket' => $S3bucket,
						'Key' => '/resources/'.$modpack->slug.'/icon.png'
					));
				}

				if (file_exists($oldPath . "/icon.png")) {
					copy($oldPath . "/icon.png", $resourcePath . "/icon.png");
					unlink($oldPath . "/icon.png");
				}
			}
		}

		if ($logo = Input::file('logo')) {
			if ($logo->isValid()) {
				$logoimg = Image::make(Input::file('logo')->getRealPath())->resize(370,220)->encode('png', 100);

				if ($success = $logoimg->save($resourcePath . '/logo.png', 100)) {
					$modpack->logo = true;

					if ($useS3) {
						$result = $client->putObject(array(
									'Bucket' => $S3bucket,
									'Key' => '/resources/'.$modpack->slug.'/logo.png',
									'Body' => $logoimg,
									'ACL' => 'public-read',
									'ContentType' => 'image/png'
								));

						$modpack->logo_url = $result['ObjectURL'];
						$modpack->logo_md5 = $result['ETag'];
					} else {
						$modpack->logo_url = URL::asset('/resources/' . $modpack->slug . '/logo.png');
						$modpack->logo_md5 = md5_file($resourcePath . "/logo.png");
					}

					if($newSlug) {
						if ($useS3) {
							$client->deleteObject(array(
								'Bucket' => $S3bucket,
								'Key' => '/resources/'.$modpack->slug.'/logo.png'
							));
						}

						if (file_exists($oldPath . "/logo.png")) {
							unlink($oldPath . "/logo.png");
						}
					}
				} else if (!$success && !$modpack->logo) {
					$modpack->logo_md5 = md5_file(public_path() . '/resources/default/logo.png');
					$modpack->logo_url = URL::asset('/resources/default/logo.png');
					return Redirect::to('modpack/edit/'.$modpack_id)->withErrors(new MessageBag(array('Failed to save new image to ' . $resourcePath . '/logo.png')));
				} else {
					Log::error('Failed to save new image to ' . $resourcePath . '/logo.png');
					return Redirect::to('modpack/edit/'.$modpack_id)->withErrors(new MessageBag(array('Failed to save new image to ' . $resourcePath . '/logo.png')));
				}
			}
		} else {
			if($newSlug) {
				if ($useS3) {
					$client->copyObject(array(
						'Bucket' => $S3bucket,
						'Key' => '/resources/'.$modpack->slug.'/logo.png',
						'CopySource' => '/resources/'.$oldSlug.'/logo.png',
						'ACL' => 'public-read',
						'ContentType' => 'image/png'
					));
					$client->deleteObject(array(
						'Bucket' => $S3bucket,
						'Key' => '/resources/'.$modpack->slug.'/logo.png'
					));
				}

				if (file_exists($oldPath . "/logo.png")) {
					copy($oldPath . "/logo.png", $resourcePath . "/logo.png");
					unlink($oldPath . "/logo.png");
				}
			}
		}

		if ($background = Input::file('background')) {
			if ($background->isValid()) {
				$backgroundimg = Image::make(Input::file('background')->getRealPath())->resize(900,600)->encode('jpg', 100);

				if ($success = $backgroundimg->save($resourcePath . '/background.jpg', 100)) {
					$modpack->background = true;

					if ($useS3) {
						$result = $client->putObject(array(
									'Bucket' => $S3bucket,
									'Key' => '/resources/'.$modpack->slug.'/background.jpg',
									'Body' => $backgroundimg,
									'ACL' => 'public-read',
									'ContentType' => 'image/jpg'
								));

						$modpack->background_url = $result['ObjectURL'];
						$modpack->background_md5 = $result['ETag'];
					} else {
						$modpack->background_url = URL::asset('/resources/' . $modpack->slug . '/background.jpg');
						$modpack->background_md5 = md5_file($resourcePath . "/background.jpg");
					}

					if($newSlug) {
						if ($useS3) {
							$client->deleteObject(array(
								'Bucket' => $S3bucket,
								'Key' => '/resources/'.$modpack->slug.'/background.jpg'
							));
						}

						if (file_exists($oldPath . "/background.jpg")) {
							unlink($oldPath . "/background.jpg");
						}
					}
				} else if (!$success && !$modpack->background) {
					$modpack->background_md5 = md5_file(public_path() . '/resources/default/background.jpg');
					$modpack->background_url = URL::asset('/resources/default/background.jpg');
					return Redirect::to('modpack/edit/'.$modpack_id)->withErrors(new MessageBag(array('Failed to save new image to ' . $resourcePath . '/background.jpg')));
				} else {
					Log::error('Failed to save new image to ' . $resourcePath . '/background.jpg');
					return Redirect::to('modpack/edit/'.$modpack_id)->withErrors(new MessageBag(array('Failed to save new image to ' . $resourcePath . '/background.jpg')));
				}
			}
		} else {
			if($newSlug) {
				if ($useS3) {
					$client->copyObject(array(
						'Bucket' => $S3bucket,
						'Key' => '/resources/'.$modpack->slug.'/background.jpg',
						'CopySource' => '/resources/'.$oldSlug.'/background.jpg',
						'ACL' => 'public-read',
						'ContentType' => 'image/jpg'
					));
					$client->deleteObject(array(
						'Bucket' => $S3bucket,
						'Key' => '/resources/'.$modpack->slug.'/background.jpg'
					));
				}

				if (file_exists($oldPath . "/background.jpg")) {
					copy($oldPath . "/background.jpg", $resourcePath . "/background.jpg");
					unlink($oldPath . "/background.jpg");
				}
			}
		}

		/* If slug changed delete old slug directory */
		if ($newSlug) {
			if (file_exists($oldPath)) {
				rmdir($oldPath);
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
		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
		{
			return Redirect::to('modpack/list/')->withErrors(new MessageBag(array('Modpack not found')));
		}

		return View::make('modpack.delete')->with(array('modpack' => $modpack));
	}

	public function postDelete($modpack_id)
	{
		$modpack = Modpack::find($modpack_id);
		if (empty($modpack))
		{
			return Redirect::to('modpack/list/')->withErrors(new MessageBag(array('Modpack not found')));
		}

		foreach ($modpack->builds as $build)
		{
			$build->modversions()->sync(array());
			$build->delete();
		}

		$modpack->clients()->sync(array());
		$modpack->delete();
		Cache::forget('modpacks');

		return Redirect::to('modpack/list/')->with('success','Modpack Deleted');
	}


	/**
	 * AJAX Methods for Modpack Manager
	 **/
	public function anyModify($action = null)
	{
		if (!Request::ajax())
			return Response::view('errors.missing', array(), 404);

		if (empty($action))
			return Response::view('errors.500', array(), 500);

		switch ($action)
		{
			case "version":
				$version_id = Input::get('version');
				$modversion_id = Input::get('modversion_id');
				$affected = DB::table('build_modversion')
							->where('build_id','=', Input::get('build_id'))
							->where('modversion_id', '=', $modversion_id)
							->update(array('modversion_id' => $version_id));
				if ($affected == 0) {
					if ($modversion_id != $version_id) {
						$status = 'failed';
					} else {
						$status = 'aborted';
					}
				} else {
					$status = 'success';
				}
				return Response::json(array(
							'status' => $status,
							'reason' => 'Rows Affected: '.$affected
							));
				break;
			case "delete":
				$affected = DB::table('build_modversion')
							->where('build_id','=', Input::get('build_id'))
							->where('modversion_id', '=', Input::get('modversion_id'))
							->delete();
				$status = 'success';
				if ($affected == 0){
					$status = 'failed';
				}
				return Response::json(array(
							'status' => $status,
							'reason' => 'Rows Affected: '.$affected
							));
				break;
			case "add":
				$build = Build::find(Input::get('build'));
				$mod = Mod::where('name','=',Input::get('mod-name'))->first();
				$ver = Modversion::where('mod_id','=', $mod->id)
									->where('version','=', Input::get('mod-version'))
									->first();
				$affected = DB::table('build_modversion')
							->where('build_id','=', $build->id)
							->where('modversion_id', '=', $ver->id)
							->get();
				$duplicate = !(empty($affected));
				if($duplicate){
					return Response::json(array(
								'status' => 'failed',
								'reason' => 'Duplicate Modversion found'
								));
				} else {
					$build->modversions()->attach($ver->id);
					return Response::json(array(
								'status' => 'success',
								'pretty_name' => $mod->pretty_name,
								'version' => $ver->version
								));
				}
				break;
			case "recommended":
				$modpack = Modpack::find(Input::get('modpack'));
				$new_version = Input::get('recommended');
				$modpack->recommended = $new_version;
				$modpack->save();

				Cache::forget('modpack.' . $modpack->slug);

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

				Cache::forget('modpack.' . $modpack->slug);

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
