<?php

class SolderController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		$this->beforeFilter('checker');
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
		$changelog = array_slice(UpdateUtils::getChangelog('latest'), 0, 10);

		$latestVersion = UpdateUtils::getLatestVersion()['name'];

		$latestData = array('version' => $latestVersion,
							'commit' => $changelog[0]);

		if (Cache::has('checker') && Cache::get('checker')) {
			$version = UpdateUtils::getCurrentVersion();
			$commit = UpdateUtils::getCurrentCommit();
			$branch = UpdateUtils::getCurrentBranch();

			$currentData = array('version' => $version,
							 'commit' => $commit,
							 'branch' => $branch,
							 'shell_exec' => UpdateUtils::isExecEnabled(),
							 'git' => UpdateUtils::isGitInstalled(),
							 'gitrepo' => UpdateUtils::isGitRepo());

			return View::make('solder.update')->with('changelog', $changelog)->with('currentData', $currentData)->with('latestData', $latestData);
		}

		$currentData = array('version' => SOLDER_VERSION,
							 'shell_exec' => UpdateUtils::isExecEnabled(),
							 'git' => UpdateUtils::isGitInstalled(),
							 'gitrepo' => UpdateUtils::isGitRepo());
		
		return View::make('solder.update')->with('changelog', $changelog)->with('currentData', $currentData)->with('latestData', $latestData);
	}

	public function getUpdateCheck()
	{
		if (Request::ajax())
		{
			$branch = UpdateUtils::getCurrentBranch();

			if(UpdateUtils::getUpdateCheck(true)){
				Cache::put('update', true, 60);
				return Response::json(array(
									'status' => 'success',
									'build' => false,
									'branch' => $branch,
									'update' => true
									));
			} else {
				Cache::forget('update');

				$changelog = array_slice(UpdateUtils::getChangelog('latest'), 0, 10)[0];
				$commit = UpdateUtils::getCurrentCommit();
				$build = boolval(strcasecmp($commit, $changelog['sha']) != 0);
				

				return Response::json(array(
									'status' => 'success',
									'build' => $build,
									'branch' => $branch,
									'update' => false
									));
			}
		}

		return App::abort(404);
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

		return App::abort(404);
	}
}