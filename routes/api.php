<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', 'ApiController@getIndex');
Route::get('modpack/{modpack?}/{build?}', 'ApiController@getModpack');
Route::get('mod/{mod?}/{version?}', 'ApiController@getMod');
Route::get('verify/{key}', 'ApiController@getVerify');