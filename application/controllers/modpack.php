<?php

class Modpack_Controller extends Base_Controller {
	public $restful = true;

	public function get_index( ) {
		$yaml = file_get_contents(Config::get('solder.repo_url').'modpacks.yml');
		$modpacks = Spyc::YAMLLoad($yaml);
		$modpacks = $modpacks['modpacks'];
		
		$plist = array();
		$blocked_packs = array('technicssp','custom1','custom2','custom3');
		foreach ($modpacks as $key => $modpack)
		{
			//array_push($plist, $key);
			if (!in_array($key, $blocked_packs))
				$plist[$key] = $modpack;
		}
		$pmod = array('modpacks' => $plist,'mirror_url' => Config::get('solder.repo_url'));
		
		return Response::json( $pmod );
	}

	public function get_details( $details ) {
		$modpack = URI::segment( 2 );
		if (URI::segment( 3 ) != NULL) {
			$request = URI::segment( 3 );
		} else { 
			$request = NULL; 
		}
		$version = URI::segment( 4 );

		$yaml = file_get_contents(Config::get('solder.repo_url').$modpack.'/modpack.yml');
		$modpackData = Spyc::YAMLLoad($yaml);

		switch( $request ) {
			case "MD5":
				$md5 = array("MD5" => md5($yaml));
				return Response::json( $md5 );
				break;
			case "build":
				if ($version == NULL OR !array_key_exists($version,$modpackData['builds'])) {
					return Response::json( array( 'error' => 'Build not found' ), 404 );
				} else {
					$buildRequest = $version;
					$versionData = $modpackData['builds'][$buildRequest];
					return Response::json( $versionData );
				}
				break;
			default:

				// Generate build list
				$modpackBuilds = array();
				foreach ($modpackData['builds'] as $build => $value)
				{
					array_push($modpackBuilds,$build);
				}

				$modpack = array(
								"name" => $modpack,
								"icon_md5" => md5(file_get_contents(Config::get('solder.repo_url').$modpack."/resources/icon.png")),
								"logo_md5" => md5(file_get_contents(Config::get('solder.repo_url').$modpack."/resources/logo_180.png")),
								"background_md5" => md5(file_get_contents(Config::get('solder.repo_url').$modpack."/resources/background.jpg")),
								"recommended" => $modpackData['recommended'],
								"latest" => $modpackData['latest'],
								"builds" => $modpackBuilds
								);
				return Response::json($modpack);
		}

		return Response::json( array( "error" => "an unspecified error occured" ) );
	}

}

?>