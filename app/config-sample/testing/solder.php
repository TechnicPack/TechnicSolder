<?php

return array(
	/**
	 * Mod Respository Location
	 *
	 * This is the location of your mod reposistory. INCLUDE a trailing slash!
	 * This can be a URL or an absolute file location.
	 *
	 **/
	'repo_location' => getenv('REPO'),

	/**
	 * Mirror Location
	 *
	 * This is where the launcher will be told to search for your files. If your
	 * repo location is already a URL you can use the same location here.
	 *
	 **/
	'mirror_url' => 'http://mirror.technicpack.net/Technic/',

	/**
	 * MD5 Connect Timeout
	 *
	 * This is the amount of time Solder will wait before giving up trying to
	 * connect to a URL to perform a MD5 checksum.
	 *
	 **/
	'md5_connect_timeout' => 5,

	/**
	 * MD5 Hashing Timeout
	 *
	 * This is the amount of time Solder will wait before giving up trying to
	 * calculate the MD5 checksum.
	 *
	 **/
	'md5_file_timeout' => 30,
);

?>
