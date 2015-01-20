<?php

class UpdateUtils {

	public static function getCheckerEnabled() {
		return self::isGitRepo();
	}
	
	public static function getCurrentVersion() {
		return str_replace(array("\r", "\n"), "", shell_exec('git describe --abbrev=0 --tags'));
	}

	public static function getCurrentCommit() {

		return `git show --format=format:%H -s`;

	}

	public static function getUpdateCheck($manual = false) {

		if ($manual) {
			Cache::forget('availableversions');
			Cache::forget('latestlog');
		}

		if (self::isGitRepo()) {
			if (version_compare(self::getLatestVersion()['name'], self::getCurrentVersion(), '>')){
				return true;
			}
		} else {
			if (version_compare(self::getLatestVersion()['name'], SOLDER_VERSION, '>')){
				return true;
			}
		}
		
		return false;
		
	}

	public static function getUpdateDetails() {
		if (self::getUpdateCheck()){
			return self::getRawRepoStatus();
		}

		return 'Nothing to report';
	}

	public static function getLatestVersion() {

		$allVersions = self::getAllVersions();
		return $allVersions[0];
	}

	public static function getAllVersions() {
		$client = new \Github\Client();

		if (Cache::has('availableversions')) {
			return Cache::get('availableversions');
		} else {
			$solderVersions = $client->api('repo')->tags('technicpack', 'technicsolder');

			Cache::put('availableversions', $solderVersions, 360); //6 hours
			return $solderVersions;
		}

	}

	public static function getCommitInfo($commit = null) {
		if (is_null($commit)){
			if (self::isGitRepo()) {
				$commit = self::getCurrentCommit();
			} else {
				$commit = self::getLatestVersion()['commit']['sha'];
			}		
		}

		$client = new \Github\Client();

		$commitHash = substr($commit, 0, 7);
		if (Cache::has($commitHash.'.json')) {
			return Cache::get($commitHash.'.json');
		} else {
			$commitJson = $client->api('repo')->commits()->show('technicpack', 'technicsolder', $commit);

			Cache::put($commitHash.'.json', $commitJson, 360); //6 hours
			return $commitJson;
		}

	}

	public static function getChangeLog($type = 'local') {
		if ($type == 'local' && self::isGitRepo()){
			return self::getLocalChangeLog();
		} else {
			return self::getLatestChangeLog();
		}

	}

	private static function getLatestChangeLog() {

		$client = new \Github\Client();
		if (Cache::has('latestlog')) {
			return Cache::get('latestlog');
		} else {
			$changelogJson = $client->api('repo')->commits()->all('technicpack', 'technicsolder', array('sha' => 'master'));

			Cache::put('latestlog', $changelogJson, 15); //15 minutes
			return $changelogJson;
		}
	}

	public static function getLocalChangeLog($currentVersion = SOLDER_VERSION) {

		/* This is debatable. A better way might be to explode the current version and manually downgrade to get the changelog */

		$allVersions = self::getAllVersions();
		if (self::isGitRepo()) {
			$currentVersion = self::getCurrentVersion();
		}

		//Calculates the place of the version 
		$versionIndex = 0;
		for ($i = 0; $i < sizeof($allVersions); $i++){
			if ($allVersions[$i]['name'] == $currentVersion){
				$versionIndex = $i;
				break;
			}
		}

		$rawInput = shell_exec('git log --pretty=format:%H~%h~%ar~%s ' . $allVersions[$versionIndex + 1]['name'] . '..' . $currentVersion);
		$cleanedInput = explode("\n", $rawInput);

		$changelog = array();
		if (sizeof($cleanedInput) >= 4){		
			foreach($cleanedInput as $commit){
				$rawCommitData = explode("~", $commit, 4);
				$commitData = array('hash' => $rawCommitData[0],
									'abr_hash' => $rawCommitData[1],
									'message' => $rawCommitData[3],
									'time_elapsed' => $rawCommitData[2]);
				array_push($changelog, $commitData);
			}		
		}

		return $changelog;

	}

	public static function isExecEnabled() {
  		$disabled = explode(',', ini_get('disable_functions'));
  		return !in_array('shell_exec', $disabled);
	}

	public static function isGitInstalled() {
		if (self::isExecEnabled()) {
			$raw = `git --version`;
			$check = explode(' ', $raw);
			if($check[0] == 'git'){
				return true;
			}
		}
		return false;
	}

	public static function isGitRepo() {
		if (self::isGitInstalled()){
			return (trim(`git rev-parse --is-inside-work-tree`) == 'true');
		}
		return false;
	}

	private static function getRawRepoStatus() {
		return `git status -sb --porcelain`;	
	}

}
