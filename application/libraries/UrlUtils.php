<?php

class UrlUtils {

	/**
	 * Gets URL contents and returns them
	 * @param  String $url
	 * @return String
	 */
	public static function get_url_contents($url)
	{
		if (!self::check_remote_file($url)) {
			return "";
		}
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	/**
	 * Uses Curl to get URL contents and returns hash
	 * @param  String $url Url Location
	 * @return String      Hash of url contents
	 */
	public static function get_remote_md5($url)
	{
		$content = self::get_url_contents($url);
		if ($content == "") {
			return "";
		} else {
			return md5($content);
		}
	}

	/**
	 * Checks if a remote file exists
	 * @param  String $url Location of file
	 * @return  Boolean Returns true if file exists
	 */
	public static function check_remote_file($url)
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($retcode == 200) {
			return true;
		} else {
			return false;
		}
	}
}