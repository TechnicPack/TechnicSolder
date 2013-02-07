<?php

class Cache_Controller extends Base_Controller {
	public $restful = true;

	public function get_index( ) {
		$yaml = file_get_contents(path('public').'checksum.yml');
		$checksum = Spyc::YAMLLoad($yaml);

		// DEBUG
		echo "<pre>";
		print_r($checksum);
	}

	public function get_update( ) {
		$checksum = file_get_contents(Config::get('solder.repo_url').'CHECKSUM.md5');
		$md5Array = explode("\n",$checksum);
		$finalized = array();
		foreach ($md5Array as $entry)
		{
			$split = explode('|',$entry);
			$md5 = $split[0];
			if (isset($split[1]))
			{
			if (preg_match("/\\\/",$split[1])) {
				$directory = explode("\\",$split[1]);
			} else {
				$directory = explode("/",$split[1]);
			}
			
				if ($directory[0] == "mods")
				{
					if (!array_key_exists($directory[1],$finalized)) {
						$finalized[$directory[1]] = array();
					}

					// Check if basemods and change up regex
					if ($directory[1] == "basemods") {
						$regex = "/-([\w\d.-]+).zip/";
						preg_match($regex,$directory[2],$versionSplit);
						$finalized[$directory[1]][$versionSplit[1]] = $md5;
					} else {
						$regex = "/" . $directory[1] . "\-(.+).zip/";
						preg_match( $regex, $directory[2], $modversion );
						$finalized[$directory[1]][$modversion[1]] = $md5;
					}
				}
			}
		}
		$yaml = Spyc::YAMLDump($finalized);

		$ymlFileName = path('public')."checksum.yml";
		$fstream = fopen($ymlFileName,'w');
		fwrite($fstream,$yaml);
		fclose($fstream);
	}

	public function get_mod( $name = NULL, $version = NULL, $md5 = NULL ) {
		$yaml = file_get_contents(path('public').'checksum.yml');
		$checksum = Spyc::YAMLLoad($yaml);

		$success = 0;

		if( isset( $name ) && isset( $version ) ) {
			if( array_key_exists( $name, $checksum ) && array_key_exists( $version, $checksum[$name] ) ) {
				$success = 1;
				if( isset( $md5 ) && $md5 == "MD5" ) {
					return Response::json( array( "MD5" => $checksum[$name][$version] ) );
				} else {
					return Response::json( array( $name => Config::get('solder.repo_url')."mods/".$name."/".$name."-".$version.".zip" ) );
				}
			}
		}

		if ($success == 0) {
			return Response::json( array("error" => "There was an error in your request"), 404 );
		}
	}
}

?>