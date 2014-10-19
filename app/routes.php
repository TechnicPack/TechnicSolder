<?php

Route::get('/', function() {
	return Redirect::to('dashboard');
});
Route::controller('api', 'ApiController');
Route::group(array('before' => 'auth'), function() {
	Route::controller('client', 'ClientController');
	Route::controller('dashboard', 'DashboardController');
	Route::controller('key', 'KeyController');
	Route::post('mod/view/(:num)', 'mod@do_modify');
	Route::post('mod/delete/(:num)', 'mod@do_delete');
	Route::post('mod/create', 'mod@do_create');
	Route::controller('mod', 'ModController');
	Route::post('/modpack/create', 'modpack@do_create');
	Route::post('/modpack/delete/(:num)', 'modpack@do_delete');
	Route::post('/modpack/edit/(:num)', 'modpack@do_edit');
	Route::post('/modpack/addbuild/(:num)', 'modpack@do_addbuild');
	Route::controller('modpack', 'ModpackController');
	Route::controller('solder', 'SolderController');
	Route::post('/user/create', 'user@do_create');
	Route::post('/user/delete/(:num)', 'user@do_delete');
	Route::controller('user', 'UserController');
	Route::controller('reminders', 'RemindersController');
});

/**
 * Authentication Routes
 **/
Route::get('login', 'BaseController@showLogin');
Route::post('login', 'BaseController@postLogin');
Route::get('logout', function() {
	Auth::logout();
	return Redirect::to('login')->with('logout','You have been logged out.');
});