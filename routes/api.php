<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [ApiController::class, 'getIndex']);

Route::get('modpack', [ApiController::class, 'getModpackIndex']);
Route::get('modpack/{modpack}', [ApiController::class, 'getModpack']);
Route::get('modpack/{modpack}/{build}', [ApiController::class, 'getModpackBuild']);

Route::get('mod/{mod?}/{version?}', [ApiController::class, 'getMod']);

Route::get('verify/{key}', [ApiController::class, 'getVerify']);
