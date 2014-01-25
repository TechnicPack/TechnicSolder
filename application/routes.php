<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your application using Laravel's RESTful routing and it
| is perfectly suited for building large applications and simple APIs.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|		Route::get('hello', function()
|		{
|			return 'Hello World!';
|		});
|
| You can even respond to more than one URI:
|
|		Route::post(array('hello', 'world'), function()
|		{
|			return 'Hello World!';
|		});
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|		Route::put('hello/(:any)', function($name)
|		{
|			return "Welcome, $name.";
|		});
|
*/

Route::get('/', function() {
	return Redirect::to('dashboard');
});
Route::controller('api');
Route::controller('dashboard');
Route::controller('solder');
Route::post('/user/create', 'user@do_create');
Route::post('/user/delete/(:num)', 'user@do_delete');
Route::controller('user');
Route::post('/modpack/create', 'modpack@do_create');
Route::post('/modpack/delete/(:num)', 'modpack@do_delete');
Route::post('/modpack/edit/(:num)', 'modpack@do_edit');
Route::post('/modpack/addbuild/(:num)', 'modpack@do_addbuild');
Route::controller('modpack');
Route::post('mod/view/(:num)', 'mod@do_modify');
Route::post('mod/delete/(:num)', 'mod@do_delete');
Route::post('mod/create', 'mod@do_create');
Route::controller('mod');
Route::post('/client/create', 'client@do_create');
Route::post('/client/delete/(:num)', 'client@do_delete');
Route::controller('client');
Route::post('/key/create', 'key@do_create');
Route::post('/key/delete/(:num)', 'key@do_delete');
Route::controller('key');

/**
 * Authentication Routes
 **/
Route::get('/login', 'base@login');
Route::post('/login', 'base@do_login');
Route::get('/logout', function() {
	Auth::logout();
	return Redirect::to('login')->with('logout','You have been logged out.');
});

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Route::get('/', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});

Route::filter('perm', function($check)
{
	$perm = (array) Auth::user()->permission;
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