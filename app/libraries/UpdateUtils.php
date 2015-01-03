<?php

class UpdateUtils {
	
	public static function getCurrentVersion() {

		return str_replace(array("\r", "\n"), "", shell_exec('git describe --abbrev=0 --tags'));
			
	}

	public static function getCurrentCommit() {

		return `git show --format=format:%H -s`;

	}

	public static function getUpdateCheck() {

		Cache::forget('availableversions');
		if (version_compare(self::getLatestVersion()['name'], self::getCurrentVersion(), '>')){
			return true;
		} else {
			return false;
		}
		
	}

	public static function getUpdateDetails() {
		if(self::getUpdateCheck()){
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
		if(is_null($commit)){
			$commit = self::getCurrentCommit();
		}

		$client = new \Github\Client();

		$commitHash = substr($commit, 0, 7);
		if (Cache::has($commit.'.json')) {
			return Cache::get($commit.'.json');
		} else {
			$commitJson = $client->api('repo')->commits()->show('technicpack', 'technicsolder', $commit);

			Cache::put($commit.'.json', $commitJson, 360); //6 hours
			return $commitJson;
		}

	}

	public static function getChangeLog() {

		/* This is debatable. A better way might be to explode the current version and manually downgrade to get the changelog */

		$allVersions = self::getAllVersions();
		$currentVersion = self::getCurrentVersion();

		//Calculates the place of the version 
		$versionIndex = 0;
		for ($i = 0; $i < sizeof($allVersions); $i++){
			if($allVersions[$i]['name'] == $currentVersion){
				$versionIndex = $i;
				break;
			}
		}

		$rawInput = shell_exec('git log --pretty=format:%H~%h~%s~%ar ' . $allVersions[$versionIndex + 1]['name'] . '..' . $currentVersion);
		$cleanedInput = explode("\n", $rawInput);

		$changelog = array();
		foreach($cleanedInput as $commit){
			$rawCommitData = explode("~", $commit);
			$commitData = array('hash' => $rawCommitData[0],
								'abr_hash' => $rawCommitData[1],
								'message' => $rawCommitData[2],
								'time_elapsed' => $rawCommitData[3]);
			array_push($changelog, $commitData);
		}

		return $changelog;

	}

	private static function getRawRepoStatus(){

		return `git status -sb --porcelain`;
		
	}

}
