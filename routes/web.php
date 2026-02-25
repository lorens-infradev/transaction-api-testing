<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $transactions = Transaction::latest()->get();
    return view('welcome', compact('transactions'));
});
