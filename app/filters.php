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

/*App::missing(function($exception)
{
	return Response::make('Missing', 404);
});*/

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
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('perm', function($check)
{
	$perm = Auth::user()->permission;
	$perm = $perm['attributes'];
	if (!$perm['solder_full'] && !$perm[$check])
	{
		return Redirect::to('dashboard')
			->with('permission','You do not have permission to access this area.');
	}
});

Route::filter('modpack', function($modpack)
{
	$perm = Auth::user()->permission;

	if (!$perm->solder_full && !in_array($modpack, $perm->modpacks))
	{
		return Redirect::to('dashboard')
			->with('permission','You do not have permission to access this area.');
	}
});

Route::filter('build', function($build)
{
	$perm = Auth::user()->permission;
	$build = Build::find($build);
	if (empty($build))
		return Redirect::to('dashboard');

	$modpack = $build->modpack;

	if (!$perm->solder_full && !in_array($modpack->id, $perm->modpacks))
	{
		return Redirect::to('dashboard')
			->with('permission','You do not have permission to access this area.');
	}
});
