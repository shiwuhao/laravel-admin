<?php

use App\Http\Controllers\Backend\ConfigController;
use App\Http\Controllers\Backend\LoginController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\PersonalController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UploadController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Http\Request;
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
Route::get('pay', [\App\Http\Controllers\Backend\PayController::class, 'pay']);


Route::post('login', [LoginController::class, 'loginByPassword']);// 登录
Route::post('logout', [LoginController::class, 'logout']);// 登出

Route::post('/uploads', [UploadController::class, 'normal']);

Route::get('test', function () {
    $user = \App\Models\User::find(1);
    dump($user->hasPermission('get,backend/test'));
    return 11;
});

Route::prefix('configs')->group(function () {
    Route::get('items', [ConfigController::class, 'configItems']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('personal')->group(function () {
        Route::get('info', [PersonalController::class, 'info']);
        Route::get('permissions', [PersonalController::class, 'permissions']);
    });

    Route::middleware('permission')->group(function () {
        Route::prefix('configs')->group(function () {
            Route::get('', [ConfigController::class, 'index']);
            Route::post('', [ConfigController::class, 'store']);
//            Route::get('items', [ConfigController::class, 'configItems']);
            Route::get('group', [ConfigController::class, 'group']);
            Route::put('group', [ConfigController::class, 'groupUpdate']);
            Route::get('{config}', [ConfigController::class, 'show']);
            Route::put('{config}', [ConfigController::class, 'update']);
            Route::delete('{config}', [ConfigController::class, 'destroy']);
        });

        Route::prefix('users')->group(function () {
            Route::get('', [UserController::class, 'index']);
            Route::post('', [UserController::class, 'store']);
            Route::get('{user}', [UserController::class, 'show']);
            Route::put('{user}', [UserController::class, 'update']);
            Route::delete('{user}', [UserController::class, 'destroy']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('', [RoleController::class, 'index']);
            Route::post('', [RoleController::class, 'store']);
            Route::get('{role}', [RoleController::class, 'show']);
            Route::put('{role}', [RoleController::class, 'update']);
            Route::delete('{role}', [RoleController::class, 'destroy']);
        });

        Route::prefix('permissions')->group(function () {
            Route::get('', [PermissionController::class, 'index']);
            Route::post('', [PermissionController::class, 'store']);
            Route::post('auto', [PermissionController::class, 'autoGenerate']);
            Route::get('{permission}', [PermissionController::class, 'show']);
            Route::put('{permission}', [PermissionController::class, 'update']);
            Route::delete('{permission}', [PermissionController::class, 'destroy']);
        });

    });

});
