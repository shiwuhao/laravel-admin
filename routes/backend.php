<?php

use App\Http\Controllers\Backend\ActionController;
use App\Http\Controllers\Backend\AllController;
use App\Http\Controllers\Backend\ConfigController;
use App\Http\Controllers\Backend\LoginController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\PersonalController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UploadController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;


Route::post('login', [LoginController::class, 'loginByPassword']);// 登录
Route::post('logout', [LoginController::class, 'logout']);// 登出

// 文件上传
Route::prefix('uploads')->group(function () {
    Route::post('single', [UploadController::class, 'single']);
    Route::post('multiple', [UploadController::class, 'multiple']);
    Route::post('oss', [UploadController::class, 'ossGetSign']);
});

Route::prefix('configs')->group(function () {
    Route::get('items', [ConfigController::class, 'configItems']);
});

Route::middleware('auth:sanctum')->group(function () {

    // 配置项
    Route::prefix('all')->group(function () {
        Route::get('menus', [AllController::class, 'menus']);
        Route::get('roles', [AllController::class, 'roles']);
        Route::get('permissions', [AllController::class, 'permissions']);
    });

    // 个人信息
    Route::prefix('personal')->group(function () {
        Route::get('info', [PersonalController::class, 'info']);
        Route::get('permissions', [PersonalController::class, 'permissions']);
        Route::get('menus', [PersonalController::class, 'menus']);
    });

    // rbac鉴权
    Route::middleware('permission')->group(function () {
        Route::get('configs/group', [ConfigController::class, 'group']);
        Route::put('configs/group', [ConfigController::class, 'groupUpdate']);
        Route::apiResource('configs', ConfigController::class);

        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('actions', ActionController::class);
        Route::apiResource('menus', MenuController::class);

        Route::post('/permissions/auto', [PermissionController::class, 'autoGenerate']);
        Route::apiResource('permissions', PermissionController::class);

    });

});
