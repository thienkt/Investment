<?php

use App\Http\Controllers\PackageController;
use Illuminate\Support\Facades\Route;

# api/packages/

Route::delete('/{id}', [PackageController::class, 'destroy'])->name('packages.delete');
Route::get('/customization', [PackageController::class, 'getCustomizedPackages'])->name('packages.customization');
Route::get('/default', [PackageController::class, 'getDefaultPackages'])->name('packages.default');
Route::get('/{id}', [PackageController::class, 'getPackageDetail'])->name('packages.detail');
Route::get('/{id}/history', [PackageController::class, 'getHistory'])->name('packages.history');
Route::post('/{id}/change-avatar', [PackageController::class, 'changeAvatar'])->name('packages.change_avatar');
Route::post('/{id}/invest', [PackageController::class, 'createTransaction'])->name('packages.create_transaction');
Route::post('/{id}/clone', [PackageController::class, 'clone'])->name('packages.clone');
Route::post('/create', [PackageController::class, 'create'])->name('packages.create');
