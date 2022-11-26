<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

# api/admin/

Route::post('/verify_credentials/{credential}/{otp}', [AdminController::class, 'verifyCredential'])->name('admin.verify-credentials');
// Route::post('/users', [AdminController::class, ''])->name('admin.verify-credentials');
Route::resource('/users', UserController::class)->only(['index', 'update', 'store', 'destroy']);
Route::delete('/users', [UserController::class, 'remove'])->name('admin.remove');
Route::post('/users/{userId}/send-verify-email', [UserController::class, 'sendVerifyEmail'])->name('admin.send-verify-mail');
