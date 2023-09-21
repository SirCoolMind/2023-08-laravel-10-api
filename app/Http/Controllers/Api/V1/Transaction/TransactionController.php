<?php

namespace App\Http\Controllers\Api\V1\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\Transaction\TransactionResource;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::all();
        return TransactionResource::collection($transactions);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        // try to think, you want to do sepa
       $transaction =  Transaction::create($request->validated());

       if($transaction){
            return response()->json([
                'message' => "Data stored succesfully",
                'data' => TransactionResource::make($transaction),
            ], 200);
       }else{
        return response()->json([
            'message' => 'Something went wrong',
        ], 500);
       }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return TransactionResource::make($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        if ($transaction) {
            return TransactionResource::make($transaction);
        } else {
            return response()->json(['message' => __('Record not found.')], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {

        $transaction->update($request->validated());
        if($transaction){
            return response()->json([
                'message' => "Data stored succesfully",
                'data' => TransactionResource::make($transaction),
            ], 200);
        }else{
            return response()->json(['message' => 'Error saving record',], 500);
        }

        return TransactionResource::make($transaction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting record',], 500);
        }
        return response()->json([
            'message' => "Data deleted succesfully",
        ], 200);
    }
}
