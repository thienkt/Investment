<?php

use App\Http\Controllers\PackageController;
use Illuminate\Support\Facades\Route;

# api/packages/

Route::delete('/{id}', [PackageController::class, 'destroy'])->name('packages.delete');
Route::get('/{id}', [PackageController::class, 'getPackageDetail'])->name('packages.detail');
Route::get('/customization', [PackageController::class, 'getCustomizedPackages'])->name('packages.customization');
Route::get('/default', [PackageController::class, 'getDefaultPackages'])->name('packages.default');
Route::get('/history/{id}', [PackageController::class, 'getHistory'])->name('packages.history');
Route::post('/change-avatar/{id}', [PackageController::class, 'changeAvatar'])->name('packages.change_avatar');
Route::post('/clone/{id}', [PackageController::class, 'clone'])->name('packages.clone');
Route::post('/create', [PackageController::class, 'create'])->name('packages.create');
