<?php

class SolderController extends BaseController {

	public function __construct()
	{
		parent::__construct();
	}

	public function getConfigure()
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

	public function getUpdate()
	{
		$rawChangeLog = UpdateUtils::getLatestChangeLog();
		$changelog = array_key_exists('error', $rawChangeLog) ? $rawChangeLog : array_slice($rawChangeLog, 0, 10);
		$latestCommit = array_key_exists('error', $rawChangeLog) ? $rawChangeLog : $rawChangeLog[0];

		$rawLatestVersion = UpdateUtils::getLatestVersion();
		$latestVersion = array_key_exists('error', $rawLatestVersion) ? $rawLatestVersion : $rawLatestVersion['name'];

		$latestData = array('version' => $latestVersion,
							'commit' => $latestCommit);
		
		return View::make('solder.update')->with('changelog', $changelog)->with('currentVersion', SOLDER_VERSION)->with('latestData', $latestData);
	}

	public function getUpdateCheck()
	{
		if (Request::ajax())
		{

			if(UpdateUtils::getUpdateCheck()){
				Cache::put('update', true, 60);
				return Response::json(array(
									'status' => 'success',
									'update' => true
									));
			} else {
				if(Cache::get('update')){
					Cache::forget('update');
				}
				return Response::json(array(
									'status' => 'success',
									'update' => false
									));
			}
		}

		return Response::view('errors.missing', array(), 404);
	}

	public function getCacheMinecraft() {
		if (Request::ajax())
		{
			$reason = '';
			try {
				$reason = MinecraftUtils::getMinecraft(true);
			}
			catch (Exception $e) {
				return Response::json(array(
									'status' => 'error',
									'reason' => $e->getMessage()
									));
			}

			if (Cache::has('minecraftversions')){
				return Response::json(array(
									'status' => 'success',
									'reason' => $reason
									));
			} else {
				return Response::json(array(
									'status' => 'error',
									'reason' => 'An unknown error has occured.'
									));
			}
		}

		return Response::view('errors.missing', array(), 404);
	}
}