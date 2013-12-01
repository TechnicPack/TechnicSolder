<?php

return array(
	/**
	 * Mod Respository Location
	 * 
	 * This is the location of your mod reposistory. INCLUDE a trailing slash!
	 * This can be a URL or an absolute file location.
	 * 
	 **/
	'repo_location' => '',

	/**
	 * Mirror Location
	 * 
	 * This is where the launcher will be told to search for your files. If your
	 * repo location is already a URL you can use the same location here.
	 * 
	 **/
	'mirror_url' => '',
	
	/**
	 * Platform API Key
	 * 
	 * Enter your platform API key if you would like to link Solder to your
	 * Platform account.
	 * 
	 **/
	'platform_key' => '',

	/**
	 * Minecraft Version API
	 * 
	 * Do not touch this field unless you have an API that returns exactly the same
	 * response as the default Technic one. This URL is used to checked what Minecraft
	 * versions are currently compatible with the launcher.
	 */
	'minecraft_api' => 'http://www.technicpack.net/api/minecraft',

);

?>