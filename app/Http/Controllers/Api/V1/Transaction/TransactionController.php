<?php

namespace App\Http\Controllers\Api\V1\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\Transaction\TransactionResource;
use App\Models\Transaction;
use App\Traits\HttpResponses;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        // * sort
        $sortBy = '';
        switch ($request->input('sort_by')) {
            case 'category':
                $sortBy = 'transaction_types.id';
                break;
            case 'money':
                $sortBy = 'spent_money';
                break;
            case 'date':
                $sortBy = 'spent_date';
                break;
            default:
                $sortBy = 'created_at';
                break;
        }
        $sortOrder = $request->input('sort_order') == "desc" ? "desc" : "asc";
        $search = $request->input('search');

        $transactions = Transaction::with($this->withRelations())
            ->when($search, function($query) use($search){
                return $query
                    ->where('remarks', 'LIKE', "%{$search}%");
            })
            ->where('user_id', \Auth::user()->id)
            ->orderBy($sortBy, $sortOrder)
            ->paginate($request->input('items_per_page'));

        if($transactions){
            return $this->success(TransactionResource::collection($transactions)->resource);
        }
        return $this->error('','No data found',404);
    }

    public function show(Transaction $transaction)
    {
        $havePermit = false;
        if($transaction->user_id == \Auth::user()->id)
            $havePermit = true;

        if($havePermit)
            return $this->success(['data' => TransactionResource::make($transaction)]);
        else
            return $this->error(['detail' => "Action not permitted"], "Action not permitted", 422);
    }

    public function store(StoreTransactionRequest $request)
    {
        \DB::beginTransaction();
        try {
            $transaction = Transaction::create($request->validated());
            $transaction->fill([
                'user_id' => \Auth::user()->id,
            ]);
            $transaction->save();
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['data' => TransactionResource::make($transaction)]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        \DB::beginTransaction();
        try {
            $transaction->update($request->validated());
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['data' => TransactionResource::make($transaction)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
        } catch (\Exception $e) {
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }
        return $this->success([],"Data deleted successfully");
    }


    private function withRelations($newRelations = []){
        return [
            'transactionType',
            'user',
        ] + $newRelations;
    }
}
