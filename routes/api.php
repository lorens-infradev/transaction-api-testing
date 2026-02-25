<?php

use App\Http\Controllers\TransactionController;
use App\Http\Middleware\LogApiRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/transactions', [TransactionController::class, 'store'])
    ->middleware(LogApiRequests::class);

Route::get('/transactions/{transaction_number}', [TransactionController::class, 'show'])
    ->middleware(LogApiRequests::class);
