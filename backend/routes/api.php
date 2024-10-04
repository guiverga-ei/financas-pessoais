<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/transactions', function () {
//     return response()->json([
//         ['description' => 'Compra de supermercado', 'amount' => -50.00, 'date' => '2024-10-01'],
//         ['description' => 'Salário', 'amount' => 1500.00, 'date' => '2024-10-05'],
//         ['description' => 'Gasolina', 'amount' => -60.00, 'date' => '2024-10-07'],
//     ]);
// });

Route::get('/transactions', [TransactionController::class, 'index']);

Route::post('/transactions', [TransactionController::class, 'store']);
