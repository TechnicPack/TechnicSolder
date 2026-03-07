<?php

use App\Http\Controllers\Api\BuildController;
use App\Http\Controllers\Api\BuildModController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\KeyController;
use App\Http\Controllers\Api\MinecraftController;
use App\Http\Controllers\Api\ModController;
use App\Http\Controllers\Api\ModpackController;
use App\Http\Controllers\Api\ModversionController;
use App\Http\Controllers\Api\TokenController;
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

Route::get('/', fn () => response()->json([
    'api' => 'TechnicSolder',
    'version' => SOLDER_VERSION,
    'stream' => SOLDER_STREAM,
]));

Route::get('modpack', [ModpackController::class, 'index']);
Route::get('modpack/{slug}', [ModpackController::class, 'show']);
Route::get('modpack/{slug}/{version}', [BuildController::class, 'show']);

Route::get('mod', [ModController::class, 'index']);
Route::get('mod/{slug}', [ModController::class, 'show']);
Route::get('mod/{slug}/{version}', [ModversionController::class, 'show']);

Route::get('verify/{key}', [KeyController::class, 'verify'])->middleware('throttle:key-verify');

// Write API
Route::middleware('auth:sanctum')->group(function () {
    Route::post('modpack', [ModpackController::class, 'store']);
    Route::put('modpack/{slug}', [ModpackController::class, 'update']);
    Route::delete('modpack/{slug}', [ModpackController::class, 'destroy']);

    Route::post('modpack/{slug}/build', [BuildController::class, 'store']);
    Route::put('modpack/{slug}/{version}', [BuildController::class, 'update']);
    Route::delete('modpack/{slug}/{version}', [BuildController::class, 'destroy']);

    Route::post('modpack/{slug}/{version}/mod', [BuildModController::class, 'store']);
    Route::put('modpack/{slug}/{version}/mod/{modSlug}', [BuildModController::class, 'update']);
    Route::delete('modpack/{slug}/{version}/mod/{modSlug}', [BuildModController::class, 'destroy']);

    Route::post('mod', [ModController::class, 'store']);
    Route::put('mod/{slug}', [ModController::class, 'update']);
    Route::delete('mod/{slug}', [ModController::class, 'destroy']);

    Route::post('mod/{slug}/version', [ModversionController::class, 'store']);
    Route::delete('mod/{slug}/{version}', [ModversionController::class, 'destroy']);

    Route::post('client', [ClientController::class, 'store']);
    Route::put('client/{uuid}', [ClientController::class, 'update']);
    Route::delete('client/{uuid}', [ClientController::class, 'destroy']);

    Route::get('token', [TokenController::class, 'index']);
    Route::post('token', [TokenController::class, 'store']);
    Route::delete('token/{tokenId}', [TokenController::class, 'destroy']);

    Route::post('minecraft/refresh', [MinecraftController::class, 'refresh']);
});
