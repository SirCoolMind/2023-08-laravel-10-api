<?php

namespace App\Http\Controllers\Api\V1\Transaction\TransactionType;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionTypeRequest;
use App\Http\Requests\Transaction\UpdateTransactionTypeRequest;
use App\Http\Resources\Transaction\TransactionTypeResource;
use App\Models\TransactionType;
use Illuminate\Support\Carbon;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class TransactionTypeController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // * sort
        $sortBy = '';
        switch ($request->input('sort_by')) {
            case 'name':
                $sortBy = 'name';
                break;
            case 'is_active':
                $sortBy = 'is_active';
                break;
            default:
                $sortBy = 'created_at';
                break;
        }
        $sortOrder = $request->input('sort_order') == "desc" ? "desc" : "asc";
        $search = $request->input('search');

        $transactionTypes = TransactionType::query()
            ->when($search, function($query) use($search){
                return $query
                    ->where('name', 'LIKE', "%{$search}%");
            })
            ->where('user_id', \Auth::user()->id)
            ->orderBy($sortBy, $sortOrder)
            ->paginate($request->input('items_per_page'));

        if($transactionTypes){
            return $this->success(TransactionTypeResource::collection($transactionTypes)->resource);
        }
        return $this->error('','No data found',404);
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
        \DB::beginTransaction();
        try {
            $transactionType =  TransactionType::create($request->validated());
            $transactionType->fill([
                'user_id' => \Auth::user()->id,
            ]);
            $transactionType->save();

        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['data' => TransactionTypeResource::make($transactionType)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionType $transactionType)
    {
        $havePermit = false;
        if($transactionType->user_id == \Auth::user()->id)
            $havePermit = true;

        if($havePermit)
            return $this->success(['data' => TransactionTypeResource::make($transactionType)]);
        else
            return $this->error(['detail' => "Action not permitted"], "Action not permitted", 422);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionType $transactionType)
    {
        if ($transactionType) {
            return $this->success(['data' => TransactionTypeResource::make($transactionType)]);
        } else {
            return response()->json(['message' => __('Record not found.')], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionTypeRequest $request, TransactionType $transactionType)
    {
        \DB::beginTransaction();
        try {
            $transactionType->update($request->validated());

        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['data' => TransactionTypeResource::make($transactionType)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionType $transactionType)
    {
        try {
            $transactionType->delete();
        } catch (\Exception $e) {
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }
        return $this->success([],"Data deleted successfully");
    }
}
