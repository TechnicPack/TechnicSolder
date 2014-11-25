<?php

class UrlUtils {

	/**
	 * Gets URL contents and returns them
	 * @param  String $url
	 * @return String
	 */
	public static function get_url_contents($url)
	{
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
		if(self::checkRemoteFile($url)){
			$content = self::get_url_contents($url);
			return md5($content);
		}
		return "";
	}

	public static function checkRemoteFile($url)
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_exec($ch);
		if(!curl_errno($ch)){
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($retcode == 200 || $retcode == 405)
				return true;
			else {
				return false;
			}
		}
		Log::error('Curl error for ' . $url . ': ' . curl_error($ch));
		curl_close($ch);
		return false;
	}
}