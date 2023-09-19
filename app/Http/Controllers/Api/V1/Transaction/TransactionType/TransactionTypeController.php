<?php

namespace App\Http\Controllers\Api\V1\Transaction\TransactionType;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionTypeRequest;
use App\Http\Requests\Transaction\UpdateTransactionTypeRequest;
use App\Http\Resources\Transaction\TransactionTypeResource;
use App\Models\TransactionType;
use Illuminate\Support\Carbon;

class TransactionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactionTypes = TransactionType::all();
        return TransactionTypeResource::collection($transactionTypes);
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
    public function store(StoreTransactionTypeRequest $request)
    {
       $transactionType =  TransactionType::create($request->validated());

       if($transactionType){
            return response()->json([
                'message' => "Data stored succesfully",
                'data' => TransactionTypeResource::make($transactionType),
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
    public function show(TransactionType $transactionType)
    {
        return TransactionTypeResource::make($transactionType);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionType $transactionType)
    {
        if ($transactionType) {
            return TransactionTypeResource::make($transactionType);
        } else {
            return response()->json(['message' => __('Record not found.')], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionTypeRequest $request, TransactionType $transactionType)
    {

        $transactionType->update($request->validated());
        if($transactionType){
            return response()->json([
                'message' => "Data stored succesfully",
                'data' => TransactionTypeResource::make($transactionType),
            ], 200);
        }else{
            return response()->json(['message' => 'Error saving record',], 500);
        }

        return TransactionTypeResource::make($transactionType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionType $transactionType)
    {
        try {
            $transactionType->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting record',], 500);
        }
        return response()->json([
            'message' => "Data deleted succesfully",
        ], 200);
    }
}
