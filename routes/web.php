<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function() {
    return Redirect::to('dashboard');
});

Route::get('api', 'ApiController@getIndex');
Route::get('api/modpack/{modpack?}/{build?}', 'ApiController@getModpack');
Route::get('api/mod/{mod?}/{version?}', 'ApiController@getMod');
Route::get('api/verify/{key}', 'ApiController@getVerify');

Route::group(['middleware' => 'auth'], function() {
    Route::get('client/list', 'ClientController@getList')->name('client.list');
    Route::get('client/create', 'ClientController@getCreate')->name('client.create');
    Route::post('client/create', 'ClientController@postCreate');
    Route::get('client/delete/{client_id}', 'ClientController@getDelete')->name('client.delete');
    Route::post('client/delete/{client_id}', 'ClientController@postDelete');

    Route::get('dashboard', 'DashboardController@getIndex')->name('dashboard');

    Route::get('key/list', 'KeyController@getList')->name('key.list');
    Route::get('key/create', 'KeyController@getCreate')->name('key.create');
    Route::post('key/create', 'KeyController@postCreate');
    Route::get('key/delete/{key_id}', 'KeyController@getDelete')->name('key.delete');
    Route::post('key/delete/{key_id}', 'KeyController@postDelete');

    Route::get('mod/list', 'ModController@getList')->name('mod.list');
    Route::get('mod/view/{mod_id}', 'ModController@getView')->name('mod.view');
    Route::get('mod/create', 'ModController@getCreate')->name('mod.create');
    Route::post('mod/create', 'ModController@postCreate');
    Route::get('mod/delete/{mod_id}', 'ModController@getDelete')->name('mod.delete');
    Route::post('mod/delete/{mod_id}', 'ModController@postDelete');
    Route::post('mod/modify/{mod_id}', 'ModController@postModify');
    Route::get('mod/rehash', 'ModController@anyRehash')->name('mod.rehash');
    Route::post('mod/rehash', 'ModController@anyRehash');
    Route::get('mod/add-version', 'ModController@anyAddVersion')->name('mod.addVersion');
    Route::post('mod/add-version', 'ModController@anyAddVersion');
    Route::get('mod/delete-version/{version_id}', 'ModController@anyDeleteVersion');

    Route::get('modpack/list', 'ModpackController@getList')->name('modpack.list');
    Route::get('modpack/view/{modpack_id}', 'ModpackController@getView')->name('modpack.view');
    Route::get('modpack/build/{build_id}', 'ModpackController@anyBuild')->name('modpack.build');
    Route::post('modpack/build/{build_id}', 'ModpackController@anyBuild');
    Route::get('modpack/add-build/{modpack_id}', 'ModpackController@getAddBuild')->name('modpack.addBuild');
    Route::post('modpack/add-build/{modpack_id}', 'ModpackController@postAddBuild');
    Route::get('modpack/create', 'ModpackController@getCreate')->name('modpack.create');
    Route::post('modpack/create', 'ModpackController@postCreate');
    Route::get('modpack/edit/{modpack_id}', 'ModpackController@getEdit')->name('modpack.edit');
    Route::post('modpack/edit/{modpack_id}', 'ModpackController@postEdit');
    Route::get('modpack/delete/{modpack_id}', 'ModpackController@getDelete')->name('modpack.delete');
    Route::post('modpack/delete/{modpack_id}', 'ModpackController@postDelete');
    Route::get('modpack/modify/{action}', 'ModpackController@anyModify')->name('modpack.modify');
    Route::post('modpack/modify/{action}', 'ModpackController@anyModify');

    Route::get('solder/configure', 'SolderController@getConfigure')->name('solder.configure');
    Route::get('solder/update', 'SolderController@getUpdate')->name('solder.update');
    Route::get('solder/update-check', 'SolderController@getUpdateCheck')->name('solder.updateCheck');
    Route::get('solder/cache-minecraft', 'SolderController@getCacheMinecraft')->name('solder.cacheMinecraft');

    Route::get('user/list', 'UserController@getList')->name('user.list');
    Route::get('user/edit/{user_id}', 'UserController@getEdit')->name('user.edit');
    Route::post('user/edit/{user_id}', 'UserController@postEdit');
    Route::get('user/create', 'UserController@getCreate')->name('user.create');
    Route::post('user/create', 'UserController@postCreate');
    Route::get('user/delete/{user_id}', 'UserController@getDelete')->name('user.delete');
    Route::post('user/delete/{user_id}', 'UserController@postDelete');
});

/**
 * Authentication Routes
 **/
Route::get('login', 'AuthController@showLogin')->name('login');
Route::post('login', 'AuthController@postLogin');
Route::get('logout', function() {
    Auth::logout();
    return Redirect::route('login')->with('logout','You have been logged out.');
})->name('logout');
