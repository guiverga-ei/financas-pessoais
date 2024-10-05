<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::apiResource('transactions', TransactionController::class);
Route::get('balance', [TransactionController::class, 'calculateBalance']);


Route::apiResource('categories', CategoryController::class);
