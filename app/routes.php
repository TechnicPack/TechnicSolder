<?php

Route::get('/', function() {
	return Redirect::to('dashboard');
});
Route::controller('api', 'ApiController');
Route::group(array('before' => 'auth'), function() {
	Route::controller('client', 'ClientController');
	Route::controller('dashboard', 'DashboardController');
	Route::controller('key', 'KeyController');
	Route::controller('mod', 'ModController');
	Route::controller('modpack', 'ModpackController');
	Route::controller('solder', 'SolderController');
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