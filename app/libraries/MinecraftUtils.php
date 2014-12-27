<?php

class MinecraftUtils {

	public static function getMinecraft() {
		$response = '';

		if (Cache::has('minecraftversions')) {
			$response = Cache::get('minecraftversions');
		} else {
			$response = self::getMinecraftVersions();
		}

		return $response;
	}

	public static function getMinecraftVersions() {
		$response = '';
		Cache::forget('minecraftversions');

		if (UrlUtils::checkRemoteFile('http://www.technicpack.net/api/minecraft', 15)){
			$response = UrlUtils::get_url_contents('http://www.technicpack.net/api/minecraft', 15);
			$response = json_decode($response, true);
			krsort($response);
			Cache::put('minecraftversions', $response, 60);
		} else {
			$response = self::getMojangVersions();
			Cache::put('minecraftversions', $response, 60);
		}

		return $response;
	}

	public static function getMojangVersions() {
		define('MINECRAFT_API', 'https://s3.amazonaws.com/Minecraft.Download/versions/versions.json');

		$rawContent = file_get_contents(MINECRAFT_API);
		$mojangVersions = json_decode($rawContent, true);

		$versions = array();

		foreach ($mojangVersions['versions'] as $versionEntry) {
			if ($versionEntry['type'] != 'release') {
				continue;
			}
			$mcVersion = $versionEntry['id'];
			$md5 = self::getMinecraftMD5($mcVersion);

			$versions[$mcVersion] = array('version' => $mcVersion, 'md5' => $md5);
		}

		krsort($versions);

		return $versions;
	}

	public static function getMinecraftMD5($MCVersion) {

		$url = 'https://s3.amazonaws.com/Minecraft.Download/versions/'.$MCVersion.'/'.$MCVersion.'.jar';

		stream_context_set_default(array('http' => array('method' => 'HEAD')));
		$md5 = get_headers($url, 1)['ETag'];

		return str_replace('"', "", $md5);
	}
}