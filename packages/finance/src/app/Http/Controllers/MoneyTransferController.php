<?php

namespace HafizRuslan\Finance\app\Http\Controllers;

use HafizRuslan\Finance\app\Enums\FinanceTypeEnum;
use HafizRuslan\Finance\app\Http\Resources\MoneyTransferResource;
use HafizRuslan\Finance\app\Models\MoneyCategory;
use HafizRuslan\Finance\app\Models\MoneyTransaction;
use HafizRuslan\Finance\app\Models\MoneyTransfer;
use Illuminate\Http\Request;

class MoneyTransferController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        // * sort
        $sortBy = '';
        switch (request()->input('sort_by.0.key')) {
            case 'date':
                $sortBy = 'transaction_date';
                break;
            default:
                $sortBy = 'id';
        }

        $descending = request()->input('sort_by.0.order') == 'desc' ? 'DESC' : 'ASC';
        $search = request()->input('filter.search');

        $startDate = request()->input('filter.start_date');
        $endDate = request()->input('filter.end_date');
        // $projectData = \App\Models\Project::find(request()->input('project_id'));

        // * fetch
        $records = MoneyTransfer::with($this->withRelations())
            ->orderBy($sortBy, $descending)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('transaction_date', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('transaction_date', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
            })
            ->paginate(request()->input('rows_per_page'));

        return MoneyTransferResource::collection($records);
    }

    public function show($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = MoneyTransfer::with($this->withRelations())
            ->find($id);

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        return new MoneyTransferResource($record);
    }

    public function store(Request $request)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $validator = $this->getValidator($request);
        $fails = $validator->fails();
        if ($fails) {
            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $validator->errors(),
            ], 500);
        }

        $record = new MoneyTransfer;

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
            $this->setTransactionItem($record);
            $record->load($this->withRelations());

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);

            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully created.'),
            'data'    => new MoneyTransferResource($record),
        ]);
    }

    public function update(Request $request, $id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $rules = [
            // 'name' => 'required|unique:kpop_eras,name,'.$id,
        ];

        $validator = $this->getValidator($request, $rules);
        $fails = $validator->fails();
        if ($fails) {
            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $validator->errors(),
            ], 500);
        }

        $record = MoneyTransfer::with($this->withRelations())
            ->find($id);

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
            $this->setTransactionItem($record);
            $record->load($this->withRelations());

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);

            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully updated.'),
            'data'    => new MoneyTransferResource($record),
        ]);
    }

    public function destroy($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = MoneyTransfer::find($id);
        if(!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        if($record->user_id != \Auth::id()) {
            return response()->json(['message' => __('Unauthorized action.')], 403);
        }

        try {
            \DB::beginTransaction();

            // Process balance recalculation
            $listOfAccountId = [
                $record->source_account_id,
                $record->target_account_id,
            ];
            $transactionDate = \Carbon\Carbon::parse($record->transaction_date)->toDateString();
            \DB::afterCommit(function () use ($listOfAccountId, $transactionDate) {
                foreach($listOfAccountId as $accountId) {
                    \HafizRuslan\Finance\app\Jobs\ProcessAccountBalance::dispatch(
                        $accountId,
                        $transactionDate
                    );
                }
            });

            $record->sourceTransaction?->delete();
            $record->targetTransaction?->delete();
            $record->delete();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);

            return response()->json([
                'message' => __('Error deleting record.'),
                'errors'  => $th->getMessage(),
            ], 500);
        }

        return response()->json(['message' => __('Record successfully deleted.')]);
    }

    private function setRecord(Request $request, MoneyTransfer $record): MoneyTransfer
    {
        $record->source_account_id = $request->input('source_account.id');
        $record->target_account_id = $request->input('target_account.id');
        $record->amount = preg_replace('/[,.]/', '', $request->input('amount'));
        $record->transaction_date = \Carbon\Carbon::parse($request->input('transaction_date'))->setTimezone(config('app.timezone'));
        $record->description = $request->input('description');
        $record->user_id = \Auth::id();

        $record->save();

        return $record;
    }

    private function setTransactionItem(MoneyTransfer $record)
    {
        $listOfAccountId = []; // list of account id to be process on account balance
        $sourceTransaction = $record->sourceTransaction;
        if(!$sourceTransaction)
            $sourceTransaction = new MoneyTransaction();

        $listOfAccountId[] = $record->source_account_id;
        $listOfAccountId[] = $sourceTransaction->money_account_id; //store previous account

        $transferCategory = $this->retrieveTransferCategory(FinanceTypeEnum::EXPENSE, $record->user_id);
        $sourceTransaction->money_category_id = $transferCategory->id;
        $sourceTransaction->type = FinanceTypeEnum::EXPENSE;
        $sourceTransaction->money_transfer_id = $record->id;
        $sourceTransaction->money_account_id = $record->source_account_id;
        $sourceTransaction->amount = $record->amount;
        $sourceTransaction->transaction_date = $record->transaction_date;
        $sourceTransaction->description = $record->description;
        $sourceTransaction->user_id = $record->user_id;
        $sourceTransaction->save();

        $targetTransaction = $record->targetTransaction;
        if(!$targetTransaction)
            $targetTransaction = new MoneyTransaction();

        $listOfAccountId[] = $record->target_account_id;
        $listOfAccountId[] = $targetTransaction->money_account_id; //store previous account

        $transferCategory = $this->retrieveTransferCategory(FinanceTypeEnum::INCOME, $record->user_id);
        $targetTransaction->money_category_id = $transferCategory->id;
        $targetTransaction->type = FinanceTypeEnum::INCOME;
        $targetTransaction->money_transfer_id = $record->id;
        $targetTransaction->money_account_id = $record->target_account_id;
        $targetTransaction->amount = $record->amount;
        $targetTransaction->transaction_date = $record->transaction_date;
        $targetTransaction->description = $record->description;
        $targetTransaction->user_id = $record->user_id;
        $targetTransaction->save();

        // Process balance recalculation
        $listOfAccountId = array_unique(array_filter($listOfAccountId));
        $transactionDate = \Carbon\Carbon::parse($record->transaction_date)->toDateString();
        \DB::afterCommit(function () use ($listOfAccountId, $transactionDate) {
            foreach($listOfAccountId as $accountId) {
                \HafizRuslan\Finance\app\Jobs\ProcessAccountBalance::dispatch(
                    $accountId,
                    $transactionDate
                );
            }
        });
    }

    private function retrieveTransferCategory($typeExpenseIncome = null, $userId = null)
    {
        if(!$typeExpenseIncome || !$userId) {
            throw new \Exception("Missing parameter inside retrieveTransferCategory();");
        }

        $transferCategory = MoneyCategory::query()
            ->where('user_id', $userId)
            ->where('name', 'Transfer')
            ->where('type', $typeExpenseIncome)
            ->first();

        if(!$transferCategory) {
            $transferCategory = new MoneyCategory;
            $transferCategory->type = $typeExpenseIncome;
            $transferCategory->name = 'Transfer';
            $transferCategory->description = 'Utilized for transfer module';
            $transferCategory->user_id = $userId;
            $transferCategory->save();
        }

        return $transferCategory;

    }

    private function withRelations($otherRelations = [])
    {
        $relations = [
            'sourceTransaction',
            'targetTransaction',
            'sourceMoneyAccount',
            'targetMoneyAccount',
        ];

        return array_merge($relations, $otherRelations);
    }

    private function getValidator($request, $otherRules = [], $otherMessages = [])
    {
        $rules = [
            'amount'            => ['required'],
            'transaction_date'  => ['required', 'date'],
            'target_account.id' => ['required'],
            'source_account.id' => ['required'],
        ];
        $rules = array_merge($rules, $otherRules);

        $messages = [];
        $messages = array_merge($messages, $otherMessages);

        $validator = \Validator::make($request->all(), $rules, $messages);

        return $validator;
    }

    private function validateScope()
    {
        $rules = [
            // 'project_id' => 'required|exists:projects,project_id',
            // 'company_id' => 'required|exists:companies,company_id',
        ];

        $validator = \Validator::make(request()->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => __('Invalid scope'),
                'errors'  => $validator->errors(),
            ], 422);
        }
    }
}
