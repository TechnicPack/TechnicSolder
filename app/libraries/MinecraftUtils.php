<?php

class MinecraftUtils {

	public static function getMinecraft($manual = false) {
		$response = array();

		if ($manual) {
			Cache::forget('minecraftversions');
		} else if (!$manual && Cache::has('minecraftversions')) {
			$response = Cache::get('minecraftversions');
		}

		$response = self::getVersions();

		return $response;
	}

	public static function getVersions() {
		$response = array();

		if (UrlUtils::checkRemoteFile('http://www.technicpack.net/api/minecraft', 15)['success']) {
			$response = UrlUtils::get_url_contents('http://www.technicpack.net/api/minecraft', 15);
			if($response['success']) {
				$response = json_decode($response['data'], true);
				krsort($response);
				Cache::put('minecraftversions', $response, 180);
				return $response;
			}
		}

		if (UrlUtils::checkRemoteFile('https://s3.amazonaws.com/Minecraft.Download/versions/versions.json', 15)['success']) {
			$response = UrlUtils::get_url_contents('https://s3.amazonaws.com/Minecraft.Download/versions/versions.json', 15);
			if($response['success']) {
				$mojangResponse = json_decode($response['data'], true);
				$versions = array();

				foreach ($mojangResponse['versions'] as $versionEntry) {
					if ($versionEntry['type'] != 'release') {
						continue;
					}
					$mcVersion = $versionEntry['id'];
					$versions[$mcVersion] = array('version' => $mcVersion);
				}

				krsort($versions);
				Cache::put('minecraftversions', $versions, 180);
				return $versions;
			}
		}

		return $response;
	}
}
