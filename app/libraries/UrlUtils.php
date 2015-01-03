<?php

class UrlUtils {

	/**
	 * Gets URL contents and returns them
	 * @param  String $url
	 * @return String
	 */
	public static function get_url_contents($url, $timeout)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Safari/537.36');
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

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
		if (Config::has('solder.md5filetimeout'))
		{
			$timeout = Config::get('solder.md5filetimeout');
		}
		else {
			$timeout = 30;
		}

		if(self::checkRemoteFile($url, $timeout)){
			$content = self::get_url_contents($url, $timeout);
			return md5($content);
		}
		return "";
	}

	public static function checkRemoteFile($url, $timeout)
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Safari/537.36');
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_exec($ch);

		//check if there are any errors
		if(!curl_errno($ch)){
			//check HTTP return code
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($retcode == 200 || $retcode == 405)
				return true;
			else {
				return false;
			}
		}

		//log the string return of the errors
		Log::error('Curl error for ' . $url . ': ' . curl_error($ch));
		curl_close($ch);
		return false;
	}
}