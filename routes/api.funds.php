<?php

use App\Http\Controllers\FundController;
use Illuminate\Support\Facades\Route;

# api/funds/

Route::get('/history/{id}', [FundController::class, 'getHistory'])->where('id', '[0-9]+')->name('funds.history');
Route::apiResource('/', FundController::class)->only(['index', 'show']);
