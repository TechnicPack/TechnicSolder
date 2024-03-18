<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KeyController;
use App\Http\Controllers\ModController;
use App\Http\Controllers\ModpackController;
use App\Http\Controllers\SolderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::redirect('client', 'client/list');
    Route::get('client/list', [ClientController::class, 'getList'])->name('client.list');
    Route::get('client/create', [ClientController::class, 'getCreate'])->name('client.create');
    Route::post('client/create', [ClientController::class, 'postCreate']);
    Route::get('client/delete/{client_id}', [ClientController::class, 'getDelete'])->name('client.delete');
    Route::post('client/delete/{client_id}', [ClientController::class, 'postDelete']);

    Route::get('dashboard', [DashboardController::class, 'getIndex'])->name('dashboard');

    Route::redirect('key', 'key/list');
    Route::get('key/list', [KeyController::class, 'getList'])->name('key.list');
    Route::get('key/create', [KeyController::class, 'getCreate'])->name('key.create');
    Route::post('key/create', [KeyController::class, 'postCreate']);
    Route::get('key/delete/{key_id}', [KeyController::class, 'getDelete'])->name('key.delete');
    Route::post('key/delete/{key_id}', [KeyController::class, 'postDelete']);

    Route::redirect('mod', 'mod/list');
    Route::get('mod/list', [ModController::class, 'getList'])->name('mod.list');
    Route::get('mod/view/{mod_id}', [ModController::class, 'getView'])->name('mod.view');
    Route::get('mod/create', [ModController::class, 'getCreate'])->name('mod.create');
    Route::post('mod/create', [ModController::class, 'postCreate']);
    Route::get('mod/import/{provider?}/{query?}', [ModController::class, 'getImport'])->name('mod.import');
    Route::get('mod/import/details/{provider}/{mod_id}', [ModController::class, 'getImportDetails'])->name('mod.import_details');
    Route::post('mod/import/details/{provider}/{mod_id}', [ModController::class, 'postImportDetails']);
    Route::get('mod/delete/{mod_id}', [ModController::class, 'getDelete'])->name('mod.delete');
    Route::post('mod/delete/{mod_id}', [ModController::class, 'postDelete']);
    Route::post('mod/modify/{mod_id}', [ModController::class, 'postModify']);
//    Route::get('mod/rehash', [ModController::class, 'anyRehash'])->name('mod.rehash');
    Route::post('mod/rehash', [ModController::class, 'anyRehash']);
//    Route::get('mod/add-version', [ModController::class, 'anyAddVersion'])->name('mod.addVersion');
    Route::post('mod/add-version', [ModController::class, 'anyAddVersion']);
    Route::post('mod/delete-version/{version_id}', [ModController::class, 'anyDeleteVersion']);
    Route::get('mod/versions/{modSlug}', [ModController::class, 'getModVersions']);

    Route::redirect('modpack', 'modpack/list');
    Route::get('modpack/list', [ModpackController::class, 'getList'])->name('modpack.list');
    Route::get('modpack/view/{modpack_id}', [ModpackController::class, 'getView'])->name('modpack.view');
    Route::get('modpack/build/{build_id}', [ModpackController::class, 'anyBuild'])->name('modpack.build');
    Route::post('modpack/build/{build_id}', [ModpackController::class, 'anyBuild']);
    Route::get('modpack/add-build/{modpack_id}', [ModpackController::class, 'getAddBuild'])->name('modpack.addBuild');
    Route::post('modpack/add-build/{modpack_id}', [ModpackController::class, 'postAddBuild']);
    Route::get('modpack/create', [ModpackController::class, 'getCreate'])->name('modpack.create');
    Route::post('modpack/create', [ModpackController::class, 'postCreate']);
    Route::get('modpack/edit/{modpack_id}', [ModpackController::class, 'getEdit'])->name('modpack.edit');
    Route::post('modpack/edit/{modpack_id}', [ModpackController::class, 'postEdit']);
    Route::get('modpack/delete/{modpack_id}', [ModpackController::class, 'getDelete'])->name('modpack.delete');
    Route::post('modpack/delete/{modpack_id}', [ModpackController::class, 'postDelete']);
//    Route::get('modpack/modify/{action}', [ModpackController::class, 'anyModify'])->name('modpack.modify');
    Route::post('modpack/modify/{action}', [ModpackController::class, 'anyModify']);

    Route::get('solder/configure', [SolderController::class, 'getConfigure'])->name('solder.configure');
    Route::get('solder/update', [SolderController::class, 'getUpdate'])->name('solder.update');
    Route::get('solder/update-check', [SolderController::class, 'getUpdateCheck'])->name('solder.updateCheck');
    Route::get('solder/cache-minecraft', [SolderController::class, 'getCacheMinecraft'])->name('solder.cacheMinecraft');

    Route::redirect('user', 'user/list');
    Route::get('user/list', [UserController::class, 'getList'])->name('user.list');
    Route::get('user/edit/{user_id}', [UserController::class, 'getEdit'])->name('user.edit');
    Route::post('user/edit/{user_id}', [UserController::class, 'postEdit']);
    Route::get('user/create', [UserController::class, 'getCreate'])->name('user.create');
    Route::post('user/create', [UserController::class, 'postCreate']);
    Route::get('user/delete/{user_id}', [UserController::class, 'getDelete'])->name('user.delete');
    Route::post('user/delete/{user_id}', [UserController::class, 'postDelete']);
});

/**
 * Authentication Routes
 **/
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'postLogin']);
Route::get('logout', function () {
    Auth::logout();

    return Redirect::route('login')->with('logout', 'You have been logged out.');
})->name('logout');
