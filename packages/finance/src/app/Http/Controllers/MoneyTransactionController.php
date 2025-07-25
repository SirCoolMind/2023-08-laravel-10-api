<?php

namespace HafizRuslan\Finance\app\Http\Controllers;

use HafizRuslan\Finance\app\Enums\FinanceTypeEnum;
use HafizRuslan\Finance\app\Http\Resources\MoneyTransactionResource;
use HafizRuslan\Finance\app\Models\MoneyTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use SirCoolMind\UploadedFiles\app\Models\UploadedFile;

class MoneyTransactionController extends \App\Http\Controllers\Controller
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
        $records = MoneyTransaction::with($this->withRelations())
            ->orderBy($sortBy, $descending)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('transaction_date', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('transaction_date', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
            })
            ->paginate(request()->input('rows_per_page'));

        return MoneyTransactionResource::collection($records);
    }

    public function show($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = MoneyTransaction::with($this->withRelations())
            ->find($id);

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        return new MoneyTransactionResource($record);
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

        $record = new MoneyTransaction();

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
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
            'data'    => new MoneyTransactionResource($record),
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

        $record = MoneyTransaction::with($this->withRelations())
            ->find($id);

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
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
            'data'    => new MoneyTransactionResource($record),
        ]);
    }

    public function destroy($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = MoneyTransaction::find($id);

        if ($record) {
            $record->delete();

            return response()->json(['message' => __('Record successfully deleted.')]);
        } else {
            return response()->json(['message' => __('Record not found.')], 404);
        }
    }

    private function setRecord($request, $record)
    {
        $isNew = $record->exists ? true : false;
        $originals = $record->getOriginal();

        $record->amount = preg_replace('/[,.]/', '', $request->input('amount'));
        $record->transaction_date = \Carbon\Carbon::parse($request->input('transaction_date'))->setTimezone(config('app.timezone'));
        $record->description = $request->input('description');
        $record->money_category_id = $request->input('money_category.id');
        $record->money_subcategory_id = $request->input('money_subcategory.id');
        $record->money_account_id = $request->input('money_account.id');
        $record->type = $request->input('type.id');

        if (empty($record->user_id)) {
            $record->user_id = \Auth::id();
        }

        $record->save();

        UploadedFile::syncFiles($record->transactionImages, $request->input('transaction_images'));

        if ($request->hasFile('transaction_images_upload')) {
            UploadedFile::store($record, MoneyTransaction::FileTypeTransactionImages, $request->file('transaction_images_upload'));
        }

        // Process balance recalculation
        $listOfAccountId = array_unique(array_filter([
            $record->getOriginal('money_account_id'),
            $record->money_account_id,
        ]));

        $originalDate = \Carbon\Carbon::parse($record->getOriginal('transaction_date'));
        $recordDate = \Carbon\Carbon::parse($record->transaction_date);
        $transactionDate = $originalDate->lessThan($recordDate) ? $originalDate : $recordDate;
        $transactionDate = $transactionDate->toDateString();
        \DB::afterCommit(function () use ($listOfAccountId, $transactionDate) {
            foreach ($listOfAccountId as $accountId) {
                \HafizRuslan\Finance\app\Jobs\ProcessAccountBalance::dispatch(
                    $accountId,
                    $transactionDate
                );
            }
        });

        return $record;
    }

    private function withRelations($otherRelations = [])
    {
        $relations = [
            'moneyCategory',
            'moneySubCategory',
            'moneyAccount',
        ];

        return array_merge($relations, $otherRelations);
    }

    private function getValidator($request, $otherRules = [], $otherMessages = [])
    {
        $rules = [
            'amount'           => ['required'],
            'transaction_date' => ['required', 'date'],
            // 'description'      => ['required'],
            // 'category'         => ['required'],
            // 'sub_category'     => ['required'],
            'money_account.id'        => ['required'],
            'money_category.id'       => ['required'],
            // 'money_subcategory.id'    => ['required'],
            'type.id'                 => ['required', new Enum(FinanceTypeEnum::class)],
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

    // Transfer logic if type equal to transfer
    // DB::transaction(function () use ($data) {
    //     $transfer = MoneyTransfer::create([
    //         'source_account_id' => $data['source_account_id'],
    //         'target_account_id' => $data['target_account_id'],
    //         'amount' => $data['amount'],
    //         'transaction_date' => $data['transaction_date'],
    //         'user_id' => auth()->id(),
    //     ]);

    //     // Create source EXPENSE transaction
    //     MoneyTransaction::create([
    //         'money_account_id' => $transfer->source_account_id,
    //         'type' => 'EXPENSE',
    //         'amount' => $transfer->amount,
    //         'transaction_date' => $transfer->transaction_date,
    //         'money_transfer_id' => $transfer->id,
    //         'user_id' => $transfer->user_id,
    //     ]);

    //     // Create target INCOME transaction
    //     MoneyTransaction::create([
    //         'money_account_id' => $transfer->target_account_id,
    //         'type' => 'INCOME',
    //         'amount' => $transfer->amount,
    //         'transaction_date' => $transfer->transaction_date,
    //         'money_transfer_id' => $transfer->id,
    //         'user_id' => $transfer->user_id,
    //     ]);
    // });

    // DB::transaction(function () use ($transfer) {
    //     // Delete both linked transactions
    //     $transfer->sourceTransaction?->delete();
    //     $transfer->targetTransaction?->delete();

    //     $transfer->delete();
    // });
}
