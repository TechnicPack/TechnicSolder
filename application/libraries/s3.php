<?php

/**
 * A LaravelPHP package for working w/ Amazon S3.
 *
 * @package    S3
 * @author     Scott Travis <scott.w.travis@gmail.com>
 * @link       http://github.com/swt83/laravel-s3
 * @license    MIT License
 */

class S3
{
	const ACL_PRIVATE = 'private';
	const ACL_PUBLIC_READ = 'public-read';
	const ACL_PUBLIC_READ_WRITE = 'public-read-write';
	const ACL_AUTHENTICATED_READ = 'authenticated-read';
	const STORAGE_CLASS_STANDARD = 'STANDARD';
	const STORAGE_CLASS_RRS = 'REDUCED_REDUNDANCY';

	public static function __callStatic($method, $args)
	{	
		// include
		require_once(__DIR__.'/../vendor/s3.php');
		
		// build object
		$s3 = new Amazon\S3(Config::get('solder.access_key'), Config::get('solder.secret_key'));
		
		// return
		return call_user_func_array(array($s3, self::camelize($method)), $args);
	}
	
	private static function camelize($word)
	{
		return lcfirst(preg_replace('/(^|_)(.)/e', "strtoupper('\\2')", strval($word)));
	}
}