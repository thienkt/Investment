<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

# api/me/

Route::get('/', [UserController::class, 'getUserInfo'])->name('user.info');
Route::post('/change-avatar', [UserController::class, 'changeAvatar'])->name('user.info');
Route::get('/settings', [UserController::class, 'getUserStatus'])->name('user.status');
Route::get('/assets', [UserController::class, 'getAssetInfo'])->name('user.asset');
Route::prefix('kyc-verification')->group(function () {
    // Route::post('/portrait', [UserController::class, ''])->name('user.portrait');
    // Route::post('/identity_image_front', [UserController::class, ''])->name('user.identity_image_front');
    // Route::post('/identity_image_back', [UserController::class, ''])->name('user.identity_image_back');
});
