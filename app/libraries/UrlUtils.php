<?php

class UrlUtils {

	/**
	 * Initializes a cURL session with common options
	 * @param  String $url
	 * @param  int $timeout
	 * @return resource
	 */
	private static function curl_init($url)
	{
		$ch = curl_init($url);

		if (Config::has('solder.md5_connect_timeout'))
		{
			$timeout = Config::get('solder.md5_connect_timeout');
			if(is_int($timeout)){
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			}
		}
		else {
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		}

		if (Config::has('solder.md5_file_timeout'))
		{
			$timeout = Config::get('solder.md5_file_timeout');
			if(is_int($timeout)){
				curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			}
		}
		else {
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'TechnicSolder/0.7 (https://github.com/TechnicPack/TechnicSolder)');
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		return $ch;
	}

	/**
	 * Gets URL contents and returns them
	 * @param  String $url
	 * @return String
	 */
	public static function get_url_contents($url)
	{
		$ch = self::curl_init($url);

		$data = curl_exec($ch);
		$info = curl_getinfo($ch);

		if(!curl_errno($ch)){
			//check HTTP return code
			curl_close($ch);
			if ($info['http_code'] == 200) {
				return array(
					'success' => true,
					'data' => $data,
					'info' => $info
				);
			} else {
				Log::error('Curl error for ' . $url . ': URL returned status code - ' . $info['http_code']);
				return array(
					'success' => false,
					'message' => 'URL returned status code - ' . $info['http_code'],
					'info' => $info
				);
			}
		}

		$errors = curl_error($ch);
		//log the string return of the errors
		Log::error('Curl error for ' . $url . ': ' . $errors);
		curl_close($ch);
		return array(
			'success' => false,
			'message' => $errors,
			'info' => $info
		);
	}

	/**
	 * Uses Curl to get URL contents and returns hash
	 * @param  String $url Url Location
	 * @return String      Hash of url contents
	 */
	public static function get_remote_md5($url)
	{
		$checkFile = self::checkRemoteFile($url);

		if($checkFile['success']){
			$content = self::get_url_contents($url);
			if($content['success']){
				try {
					$md5 = md5($content['data']);
					return array(
						'success' => true,
						'md5' => $md5,
						'filesize' => $content['info']['download_content_length']
					);
				} catch (Exception $e) {
					Log::error('Error hashing remote md5: '. $e->getMessage());
					return array(
						'success' => false,
						'message' => $e->getMessage(),
						'info' => $content['info'],
					);
				}
			} else {
				return array(
					'success' => false,
					'message' => $content['message'],
					'info' => $content['info'],
				);
			}
		}

		return array(
			'success' => false,
			'message' => $checkFile['message'],
			'info' => $checkFile['info']
		);
	}

	public static function checkRemoteFile($url)
	{
		$ch = self::curl_init($url);

		curl_setopt($ch, CURLOPT_NOBODY, true);

		curl_exec($ch);

		$info = curl_getinfo($ch);

		//check if there are any errors
		if(!curl_errno($ch)){
			//check HTTP return code
			curl_close($ch);
			if ($info['http_code'] == 200 || $info['http_code'] == 405)
				return array('success' => true, 'info' => $info);
			else {
				return array(
					'success' => false,
					'message' =>'URL returned status code - ' . $info['http_code'],
					'info' => $info
				);
			}
		}

		//log the string return of the errors
		$errors = curl_error($ch);
		Log::error('Curl error for ' . $url . ': ' . $errors);
		curl_close($ch);
		return array('success' => false, 'message' => $errors, 'info' => $info);
	}

	public static function getHeaders($url){
		$ch = self::curl_init($url);

		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_HEADER, true);

		$data = curl_exec($ch);
		$info = curl_getinfo($ch);

		if(!curl_errno($ch)){
			//check HTTP return code
			curl_close($ch);
			if ($info['http_code'] == 200 || $info['http_code'] == 405)
				return array('success' => true, 'headers' => $data, 'info' => $info);
			else {
				return array('success' => false, 'message' =>
				'Remote server did not return 200', 'info' => $info);
			}
		}

		//log the string return of the errors
		$errors = curl_error($ch);
		Log::error('Curl error for ' . $url . ': ' . $errors);
		curl_close($ch);
		return array('success' => false, 'message' => $errors, 'info' => $info);
	}
}
