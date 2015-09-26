<?php

class UrlUtils {

	/**
	 * Initializes a cURL session with common options
	 * @param  String $url
	 * @param  int $timeout
	 * @return resource
	 */
	private static function curl_init($url, $timeout)
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'TechnicSolder/0.7 (https://github.com/TechnicPack/TechnicSolder)');
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

		return $ch;
	}

	/**
	 * Gets URL contents and returns them
	 * @param  String $url
	 * @return String
	 */
	public static function get_url_contents($url, $timeout)
	{
		$ch = self::curl_init($url, $timeout);

		$data = curl_exec($ch);

		if(!curl_errno($ch)){
			//check HTTP return code
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($retcode == 200 || $retcode == 405)
				return $data;
			else {
				return false;
			}
		}

		//log the string return of the errors
		Log::error('Curl error for ' . $url . ': ' . curl_error($ch));
		curl_close($ch);
		return false;
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
			if($content){
				return md5($content);
			}
		}
		return "";
	}

	public static function checkRemoteFile($url, $timeout)
	{
		$ch = self::curl_init($url, $timeout);

		curl_setopt($ch, CURLOPT_NOBODY, true);

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

	public static function getHeaders($url, $timeout){
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_USERAGENT, 'TechnicSolder/0.7 (www.techincpack.net)');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

		$data = curl_exec($ch);

		if(!curl_errno($ch)){
			//check HTTP return code
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($retcode == 200 || $retcode == 405)
				return $data;
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
