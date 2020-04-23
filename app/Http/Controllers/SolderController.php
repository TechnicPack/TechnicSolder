<?php namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use App\Libraries\MinecraftUtils;
use App\Libraries\UpdateUtils;

class SolderController extends Controller {

	public function getConfigure()
	{
		if (Request::has('edit-solder'))
		{
			Config::set('solder.mirror_url',Request::input('mirror_url'));
			Config::set('solder.repo_location',Request::input('repo_location'));
			Config::set('solder.platform_key',Request::input('platform_key'));
			return Redirect::to('solder/configure')
				->with('success','Your solder configuration has been updated.');
		}
		return view('solder.configure');
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
		
		return view('solder.update')->with('changelog', $changelog)->with('currentVersion', SOLDER_VERSION)->with('latestData', $latestData);
	}

	public function getUpdateCheck()
	{
		if (Request::ajax())
		{

			if(UpdateUtils::getUpdateCheck()){
				Cache::put('update', true, now()->addMinutes(60));
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