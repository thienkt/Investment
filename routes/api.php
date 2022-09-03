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
        Route::post('/me/change-avatar', [UserController::class, 'changeAvatar'])->name('user.info');
        Route::get('/settings', [UserController::class, 'getUserStatus'])->name('user.status');
        Route::prefix('kyc-verification')->group(function () {
            // Route::post('/portrait', [UserController::class, ''])->name('user.portrait');
            // Route::post('/identity_image_front', [UserController::class, ''])->name('user.identity_image_front');
            // Route::post('/identity_image_back', [UserController::class, ''])->name('user.identity_image_back');
        });

        Route::get('/funds/history/{id}', [FundController::class, 'getHistory'])->where('id', '[0-9]+')->name('funds.history');
        Route::apiResource('funds', FundController::class)->only(['index', 'show']);

        Route::prefix('packages')->group(function () {
            Route::get('/default', [PackageController::class, 'getDefaultPackages'])->name('packages.default');
            Route::get('/customization', [PackageController::class, 'getCustomizedPackages'])->name('packages.customization');
            Route::get('/{id}', [PackageController::class, 'getPackageDetail'])->name('packages.detail');
            Route::get('/history/{id}', [PackageController::class, 'getHistory'])->name('packages.history');
            Route::post('/create', [PackageController::class, 'create'])->name('packages.create');
            Route::post('/clone/{id}', [PackageController::class, 'clone'])->name('packages.clone');
            Route::post('/change-avatar/{id}', [PackageController::class, 'changeAvatar'])->name('packages.change_avatar');
            Route::put('/update/{id}', [PackageController::class, 'update'])->name('packages.update');
            Route::delete('/{id}', [PackageController::class, 'destroy'])->name('packages.delete');
        });
    }
);

Route::get('/assets/{uid}/{name}',  [ImageController::class, 'getImage'])->name('packages.asset');
