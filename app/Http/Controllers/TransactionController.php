<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * Store a newly created transaction in storage.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = Transaction::create($request->validated());

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => $transaction,
        ], 201);
    }

    /**
     * Display the specified transaction by transaction_number.
     */
    public function show(string $transaction_number): JsonResponse
    {
        $transaction = Transaction::where('transaction_number', $transaction_number)->first();

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction non-existing',
            ], 404);
        }

        return response()->json([
            'message' => 'Transaction retrieved successfully',
            'data' => [
                'transaction_number' => $transaction->transaction_number,
                'amount' => $transaction->amount,
                'description' => $transaction->description,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at,
            ]
        ], 200);
    }
}
