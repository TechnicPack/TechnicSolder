<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

App::missing(function($exception)
{
	return Response::view('errors.500', array('code' => 404, 'exception' => new Exception('Page not found')), 404);
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});

Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('solder_users', function()
{
	$user = Request::segment(3);
	$action = Request::segment(2);
	$perm = Auth::user()->permission;
	$perm = $perm['attributes'];

	if (!$perm['solder_full'] && !$perm['solder_users'])
	{
		/* This allows the user to edit thier own profile */
		if ($action == 'edit'){
			if ($user != Auth::user()->id){
				return Redirect::to('dashboard')
					->with('permission','You do not have permission to access this area.');
			}
		}
		else
		{
			return Redirect::to('dashboard')
					->with('permission','You do not have permission to access this area.');
		}
	}
});

Route::filter('solder_keys', function()
{
	$perm = Auth::user()->permission;
	$perm = $perm['attributes'];
	if (!$perm['solder_full'] && !$perm['solder_keys'])
	{
		return Redirect::to('dashboard')
			->with('permission','You do not have permission to access this area.');
	}
});

Route::filter('solder_clients', function()
{
	$perm = Auth::user()->permission;
	$perm = $perm['attributes'];
	if (!$perm['solder_full'] && !$perm['solder_clients'])
	{
		return Redirect::to('dashboard')
			->with('permission','You do not have permission to access this area.');
	}
});

Route::filter('solder_modpacks', function()
{
	$check = '';
	switch(Request::segment(2)){
		case 'create':
		$check = 'modpacks_create';
		break;
		case 'delete':
		$check = 'modpacks_delete';
		break;
		default:
		$check = 'modpacks_manage';
		break;
	}
	$perm = Auth::user()->permission;
	$perm = $perm['attributes'];
	if (!$perm['solder_full'] && !$perm[$check])
	{
		return Redirect::to('dashboard')
			->with('permission','You do not have permission to access this area.');
	}
});

Route::filter('solder_mods', function()
{
	$check = '';
	switch(Request::segment(2)){
		case 'create':
		$check = 'mods_create';
		break;
		case 'delete':
		$check = 'mods_delete';
		break;
		case 'edit':
		$check = 'mods_manage';
		break;
		case 'view':
		$check = 'mods_manage';
		break;
		case 'list':
		$check = 'mods_manage';
		break;
		default:
		return Redirect::to('mods/list');
		break;
	}
	$perm = Auth::user()->permission;
	$perm = $perm['attributes'];
	if (!$perm['solder_full'] && !$perm[$check])
	{
		return Redirect::to('dashboard')
			->with('permission','You do not have permission to access this area.');
	}
});

Route::filter('modpack', function()
{
	$modpack = Request::segment(3);
	$perm = Auth::user()->permission;

	if (empty($modpack))
		return Redirect::to('dashboard');

	if (!$perm->solder_full && !in_array($modpack, $perm->modpacks))
	{
		return Redirect::to('dashboard')
			->with('permission','You do not have permission to access this area.');
	}
});

Route::filter('build', function()
{
	$buildId = Request::segment(3);
	$build = Build::find($buildId);

	if (empty($build))
		return Redirect::to('dashboard');

	$modpack = $build->modpack;

	$perm = Auth::user()->permission;

	if (!$perm->solder_full && !in_array($modpack->id, $perm->modpacks))
	{
		return Redirect::to('dashboard')
			->with('permission','You do not have permission to access this area.');
	}
});
