<?php

class MinecraftUtils {

	public static function getMinecraft($manual = false) {
		$response = '';

		if ($manual) {
			Cache::forget('minecraftversions');
		}

		if (Cache::has('minecraftversions')) {
			$response = Cache::get('minecraftversions');
		} else {
			$response = self::getVersions();
		}

		return $response;
	}

	public static function getVersions() {
		$response = '';

		/*if ($success = UrlUtils::checkRemoteFile('http://www.technicpack.net/api/minecraft', 15)) {
			$response = UrlUtils::get_url_contents('http://www.technicpack.net/api/minecraft', 15);
			if(!empty($response)) {
				$response = json_decode($response, true);
				krsort($response);
				Cache::put('minecraftversions', $response, 180);
				return $response;
			}
		}*/

		if ($success = UrlUtils::checkRemoteFile('https://s3.amazonaws.com/Minecraft.Download/versions/versions.json', 15)) {
			$response = UrlUtils::get_url_contents('https://s3.amazonaws.com/Minecraft.Download/versions/versions.json', 15);
			if(!empty($response)) {
				$mojangResponse = json_decode($response, true);

				$versions = array();

				foreach ($mojangResponse['versions'] as $versionEntry) {
					if ($versionEntry['type'] != 'release') {
						continue;
					}
					$mcVersion = $versionEntry['id'];
					$md5 = self::getMojangMD5($mcVersion);

					$versions[$mcVersion] = array('version' => $mcVersion, 'md5' => $md5);
				}

				krsort($versions);
				Cache::put('minecraftversions', $versions, 180);
				return $versions;
			}
		}

		return $response;
	}

	private static function getMojangMD5($MCVersion) {

		$url = 'https://s3.amazonaws.com/Minecraft.Download/versions/'.$MCVersion.'/'.$MCVersion.'.jar';

		$response = get_headers($url, 1);
		if(!empty($response)) {
				return str_replace('"', '', $response['ETag']);
		}
		return '';
	}
}
