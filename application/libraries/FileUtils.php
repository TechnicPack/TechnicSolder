<?php

class FileUtils {

	public static function check_resource($slug,$resource)
	{
		$url = Config::get('solder.repo_location').$slug.'/resources/'.$resource;
		if (file_exists($url))
			return true;
		else
		{
			$ch = curl_init($url);

			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_exec($ch);
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($retcode == 200)
				return true;
			else {
				return false;
			}
		}
	}
	
}