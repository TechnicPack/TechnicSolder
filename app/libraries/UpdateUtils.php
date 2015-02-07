<?php

class UpdateUtils {

	public static function getCheckerEnabled() {
		if($currentVersion = Cache::get('checkerenabled')) {
			return $currentVersion;
		} else {
			$enabled = self::isGitRepo();
			Cache::put('checkerenabled', $enabled, 1440);
			return $enabled;
		}
	}
	
	public static function getCurrentVersion() {
		if($currentVersion = Cache::get('currentversion')) {
			return $currentVersion;
		} else {
			$version = 'v0.7.0.9';
			if(self::isGitRepo()) {
				$version = str_replace(array("\r", "\n"), "", shell_exec('git describe --abbrev=0 --tags'));
			}
			Cache::put('currentversion', $version, 360);
			return $version;
		}
	}

	public static function getCurrentCommit() {
		if($currentCommit = Cache::get('currentcommit')) {
			return $currentCommit;
		} else {
			$commit = str_replace(array("\r", "\n"), "", shell_exec('git show --format=format:%H -s'));
			Cache::put('currentcommit', $commit, 360);
			return $commit;
		}
	}

	public static function getCurrentBranch() {
		if($currentBranch = Cache::get('currentbranch')) {
			return $currentBranch;
		} else {
			$branch = str_replace(array("\r", "\n"), "", shell_exec('git rev-parse --abbrev-ref HEAD'));
			Cache::put('currentbranch', $branch, 360);
			return $branch;
		}
	}

	public static function getUpdateCheck($manual = false) {

		if ($manual) {
			Cache::forget('checkerenabled');			
			Cache::forget('availableversions');
			Cache::forget('latestlog');
			Cache::forget('currentversion');
			Cache::forget('currentcommit');
			Cache::forget('currentbranch');
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

		if ($availversions = Cache::get('availableversions')) {
			return $availversions;
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
		if ($commitinfo = Cache::get($commitHash.'-json')) {
			return $commitinfo;
		} else {
			$commitJson = $client->api('repo')->commits()->show('technicpack', 'technicsolder', $commit);

			Cache::put($commitHash.'-json', $commitJson, 360); //6 hours
			return $commitJson;
		}

	}

	public static function getChangeLog($type = 'local') {
		if ($type == 'local' && self::isGitRepo()){
			return self::getLocalChangeLog();
		} else {
			return self::getLatestChangeLog(self::getCurrentBranch());
		}

	}

	private static function getLatestChangeLog($branch = 'master') {

		$client = new \Github\Client();
		if ($latestlog = Cache::get('latestchangelog')) {
			return $latestlog;
		} else {
			$changelogJson = $client->api('repo')->commits()->all('technicpack', 'technicsolder', array('sha' => $branch));

			Cache::put('latestchangelog', $changelogJson, 60);
			return $changelogJson;
		}
	}

	public static function getLocalChangeLog($currentVersion = SOLDER_VERSION) {

		$allVersions = self::getAllVersions();
		if (self::isGitRepo()) {
			$currentVersion = self::getCurrentVersion();
		}

		if ($changelog = Cache::get('localchangelog'.$currentVersion)) {
			return $changelog;
		} else {
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
			if (sizeof($cleanedInput) >= 4) {
				foreach($cleanedInput as $commit){
					$rawCommitData = explode("~", $commit, 4);
					$commitData = array('hash' => $rawCommitData[0],
										'abr_hash' => $rawCommitData[1],
										'message' => $rawCommitData[3],
										'time_elapsed' => $rawCommitData[2]);
					array_push($changelog, $commitData);
				}
			}
			Cache::put('localchangelog'.$currentVersion, $changelog, 360);
			return $changelog;
		}
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
			return false;
		}
		return false;
	}

	public static function isGitRepo() {
		if (self::isGitInstalled()){
			return trim(`git rev-parse --is-inside-work-tree`) == 'true';
		}
		return false;
	}

	private static function getRawRepoStatus() {
		return `git status -sb --porcelain`;	
	}

}
