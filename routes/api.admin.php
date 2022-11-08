<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

# api/admin/

Route::post('/verify_credentials/{credential}/{otp}', [AdminController::class, 'verifyCredential'])->name('admin.verify-credentials');
