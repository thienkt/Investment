<?php

use App\Http\Controllers\FundController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/me', [UserController::class, 'getUserInfo'])->name('user.info');
        Route::get('/funds/history/{id}', [FundController::class, 'getHistory'])->where('id', '[0-9]+')->name('funds.history');
        Route::apiResource('funds', FundController::class)->only(['index', 'show']);
        Route::prefix('packages')->group(function () {
            Route::get('/default', [PackageController::class, 'getDefaultPackages'])->name('packages.default');
            Route::get('/customization', [PackageController::class, 'getCustomizedPackages'])->name('packages.customization');
            Route::post('/create', [PackageController::class, 'create'])->name('packages.create');
            Route::post('/clone/{id}', [PackageController::class, 'clone'])->name('packages.clone');
            Route::post('/change-avatar/{id}', [PackageController::class, 'changeAvatar'])->name('packages.change_avatar');
        });
    }
);

Route::get('/assets/{uid}/{name}',  [ImageController::class, 'getImage'])->name('packages.asset');
