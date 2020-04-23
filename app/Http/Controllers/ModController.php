<?php namespace App\Http\Controllers;

use App\Mod;
use App\Modversion;
use Exception;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use App\Libraries\UrlUtils;

class ModController extends Controller {

	public function __construct()
	{
		$this->middleware('solder_mods');
	}

    public function getIndex()
	{
		return Redirect::to('mod/list');
	}

	public function getList()
	{
		$mods = Mod::with(
				array(
					'versions' => function($query){
						$query->orderBy('modversions.updated_at', 'desc');
					}
				)
			)
			->get();
		return view('mod.list')->with(array('mods' => $mods));
	}

	public function getView($mod_id = null)
	{
		$mod = Mod::find($mod_id);
		if (empty($mod))
			return Redirect::to('mod/list')->withErrors(new MessageBag(array('Mod not found')));

		return view('mod.view')->with(array('mod' => $mod));
	}

	public function getCreate()
	{
		return view('mod.create');
	}

	public function postCreate()
	{
		$rules = array(
			'name' => 'required|unique:mods',
			'pretty_name' => 'required',
			'link' => 'nullable|url',
			);
		$messages = array(
			'name.required' => 'You must fill in a mod slug name.',
			'name.unique' => 'The slug you entered is already taken',
			'pretty_name.required' => 'You must enter in a mod name',
			'link.url' => 'You must enter a properly formatted Website',
			);

		$validation = Validator::make(Request::all(), $rules, $messages);
		if ($validation->fails())
			return Redirect::to('mod/create')->withErrors($validation->messages());

		$mod = new Mod();
		$mod->name = Str::slug(Request::input('name'));
		$mod->pretty_name = Request::input('pretty_name');
		$mod->author = Request::input('author');
		$mod->description = Request::input('description');
		$mod->link = Request::input('link');
		$mod->save();
		return Redirect::to('mod/view/'.$mod->id);
	}

	public function getDelete($mod_id = null)
	{
		$mod = Mod::find($mod_id);
		if (empty($mod))
			return Redirect::to('mod/list')->withErrors(new MessageBag(array('Mod not found')));

		return view('mod.delete')->with(array('mod' => $mod));
	}

	public function postModify($mod_id = null)
	{
		$mod = Mod::find($mod_id);
		if (empty($mod))
			return Redirect::to('mod/list')->withErrors(new MessageBag(array('Error modifying mod - Mod not found')));

		$rules = array(
			'pretty_name' => 'required',
			'name' => 'required|unique:mods,name,'.$mod->id,
			'link' => 'nullable|url',
			);

		$messages = array(
			'name.required' => 'You must fill in a mod slug name.',
			'name.unique' => 'The slug you entered is already in use by another mod',
			'pretty_name.required' => 'You must enter in a mod name',
			'link.url' => 'You must enter a properly formatted Website',
			);

		$validation = Validator::make(Request::all(), $rules, $messages);
		if ($validation->fails())
			return Redirect::to('mod/view/'.$mod->id)->withErrors($validation->messages());

		$mod->pretty_name = Request::input('pretty_name');
		$mod->name = Request::input('name');
		$mod->author = Request::input('author');
		$mod->description = Request::input('description');
		$mod->link = Request::input('link');
		$mod->save();
		Cache::forget('mod.'.$mod->name);

		return Redirect::to('mod/view/'.$mod->id)->with('success','Mod successfully edited.');
	}

	public function postDelete($mod_id = null)
	{
		$mod = Mod::find($mod_id);
		if (empty($mod))
			return Redirect::to('mod/list')->withErrors(new MessageBag(array('Error deleting mod - Mod not found')));

		foreach ($mod->versions as $ver)
		{
			$ver->builds()->sync(array());
			$ver->delete();
		}
		$mod->delete();
		Cache::forget('mod.'.$mod->name);

		return Redirect::to('mod/list')->with('success','Mod deleted!');
	}

	public function anyRehash()
	{
		if (Request::ajax())
		{
			$md5 = Request::input('md5');
			$ver_id = Request::input('version-id');
			if (empty($ver_id))
				return Response::json(array(
									'status' => 'error',
									'reason' => 'Missing Post Data',
									));

			$ver = Modversion::find($ver_id);
			if (empty($ver))
				return Response::json(array(
									'status' => 'error',
									'reason' => 'Could not pull mod version from database',
									));

			if (empty($md5)) {
				$file_md5 = $this->mod_md5($ver->mod,$ver->version);
				if($file_md5['success'])
					$md5 = $file_md5['md5'];
			} else {
				$file_md5 = $this->mod_md5($ver->mod,$ver->version);
				$pfile_md5 = !$file_md5['success'] ? "Null" : $file_md5['md5'];
			}

			if ($file_md5['success'] && !empty($md5)) {
				if($md5 == $file_md5['md5']) {
					$ver->filesize = $file_md5['filesize'];
					$ver->md5 = $md5;
					$ver->save();
					return Response::json(array(
								'status' => 'success',
								'version_id' => $ver->id,
								'md5' => $ver->md5,
								'filesize' => $ver->humanFilesize("MB"),
								));
				} else {
					$ver->filesize = $file_md5['filesize'];
					$ver->md5 = $md5;
					$ver->save();
					return Response::json(array(
								'status' => 'warning',
								'version_id' => $ver->id,
								'md5' => $ver->md5,
								'filesize' => $ver->humanFilesize("MB"),
								'reason' => 'MD5 provided does not match file MD5: ' . $pfile_md5,
								));
				}
			} else {
				return Response::json(array(
							'status' => 'error',
							'reason' => 'Remote MD5 failed. ' . $file_md5['message'],
							));
			}
		}

		return Response::view('errors.missing', array(), 404);
	}

	public function anyAddVersion()
	{
		if (!Request::ajax()) {
            abort(404);
        }

        $mod_id = Request::input('mod-id');
        $md5 = Request::input('add-md5');
        $version = Request::input('add-version');
        if (empty($mod_id) || empty($version))
            return Response::json(array(
                        'status' => 'error',
                        'reason' => 'Missing Post Data'
                        ));

        $mod = Mod::find($mod_id);
        if (empty($mod))
            return Response::json(array(
                        'status' => 'error',
                        'reason' => 'Could not pull mod from database'
                        ));

        if (Modversion::where([
            'mod_id' => $mod_id,
            'version' => $version,
        ])->count() > 0) {
            return Response::json([
                'status' => 'error',
                'reason' => 'That mod version already exists',
            ]);
        }

        if (empty($md5)) {
            $file_md5 = $this->mod_md5($mod,$version);
            if($file_md5['success'])
                $md5 = $file_md5['md5'];
        } else {
            $file_md5 = $this->mod_md5($mod,$version);
            $pfile_md5 = !$file_md5['success'] ? "Null" : $file_md5['md5'];
        }

        $ver = new Modversion();
        $ver->mod_id = $mod->id;
        $ver->version = $version;

        if ($file_md5['success'] && !empty($md5)) {
            if($md5 == $file_md5['md5']) {
                $ver->filesize = $file_md5['filesize'];
                $ver->md5 = $md5;
                $ver->save();
                return Response::json(array(
                            'status' => 'success',
                            'version' => $ver->version,
                            'md5' => $ver->md5,
                            'filesize' => $ver->humanFilesize("MB"),
                            ));
            } else {
                $ver->filesize = $file_md5['filesize'];
                $ver->md5 = $md5;
                $ver->save();
                return Response::json(array(
                            'status' => 'warning',
                            'version' => $ver->version,
                            'md5' => $ver->md5,
                            'filesize' => $ver->humanFilesize("MB"),
                            'reason' => 'MD5 provided does not match file MD5: ' . $pfile_md5,
                            ));
            }
        } else {
            return Response::json(array(
                        'status' => 'error',
                        'reason' => 'Remote MD5 failed. ' . $file_md5['message'],
                        ));
        }
	}

	public function anyDeleteVersion($ver_id = null)
	{
		if (Request::ajax())
		{
			if (empty($ver_id))
				return Response::json(array(
							'status' => 'error',
							'reason' => 'Missing Post Data'
							));

			$ver = Modversion::find($ver_id);
			if (empty($ver))
				return Response::json(array(
							'status' => 'error',
							'reason' => 'Could not pull mod version from database'
							));

			$old_id = $ver->id;
			$old_version = $ver->version;
			$ver->delete();
			return Response::json(array(
									'status' => 'success',
									'version' => $old_version,
									'version_id' => $old_id
									));
		}

		return Response::view('errors.missing', array(), 404);
	}

	private function mod_md5($mod, $version)
	{
		$location = Config::get('solder.repo_location');
		$URI = $location.'mods/'.$mod->name.'/'.$mod->name.'-'.$version.'.zip';

		if (file_exists($URI)) {
			Log::info('Found \'' . $URI . '\'');
			try {
				$filesize = filesize($URI);
				$md5 = md5_file($URI);
				return array('success' => true, 'md5' => $md5, 'filesize' => $filesize);
			} catch (Exception $e) {
				Log::error("Error attempting to md5 the file: " . $URI);
				return array('success' => false, 'message' => $e->getMessage());
			}
		} else if(filter_var($URI, FILTER_VALIDATE_URL)) {
			Log::warning('File \'' . $URI . '\' was not found.');
			return $this->remote_mod_md5($mod, $version, $location);
		} else {
			$error = $URI . ' is not a valid URI';
			Log::error($error);
			return array('success' => false, 'message' => $error);
		}
	}

	private function remote_mod_md5($mod, $version, $location, $attempts = 0)
	{
		$URL = $location.'mods/'.$mod->name.'/'.$mod->name.'-'.$version.'.zip';

		$hash = UrlUtils::get_remote_md5($URL);

		if (!($hash['success']) && $attempts <= 3) {
			Log::warning("Error attempting to remote MD5 file " . $mod->name . " version " . $version . " located at " . $URL .".");
			return $this->remote_mod_md5($mod, $version, $location, $attempts + 1);
		}

		return $hash;
	}
}
