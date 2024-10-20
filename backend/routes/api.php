<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::apiResource('transactions', TransactionController::class);
Route::get('balance', [TransactionController::class, 'calculateBalance']);
Route::get('dashboard', [TransactionController::class, 'getDashboardData']);

Route::apiResource('categories', CategoryController::class);

Route::resource('accounts', AccountController::class);
