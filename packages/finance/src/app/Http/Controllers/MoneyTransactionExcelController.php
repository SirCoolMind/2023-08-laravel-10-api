<?php

namespace HafizRuslan\Finance\app\Http\Controllers;

use HafizRuslan\Finance\app\Exports\MoneyTransactionTemplateExport;
use HafizRuslan\Finance\app\Imports\MoneyTransactionBatchImport;
use HafizRuslan\Finance\app\Models\MoneyAccount;
use HafizRuslan\Finance\app\Models\MoneyCategory;
use HafizRuslan\Finance\app\Models\MoneySubCategory;
use HafizRuslan\Finance\app\Models\MoneyTransaction;
use HafizRuslan\Finance\app\Models\MoneyTransactionExcelBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class MoneyTransactionExcelController extends \App\Http\Controllers\Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new MoneyTransactionTemplateExport, 'transaction_import_template.xlsx');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $batchNo = (string) Str::uuid();
        $userId = \Auth::id() ?? 1; // Fallback for testing if no auth

        try {
            Excel::import(new MoneyTransactionBatchImport($batchNo, $userId), $request->file('file'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing file: ' . $e->getMessage()
            ], 500);
        }

        // After import, run an additional validation pass against database
        $this->validateBatchAgainstDatabase($batchNo);

        return response()->json([
            'message' => 'File uploaded and parsed successfully.',
            'batch_no' => $batchNo
        ]);
    }

    public function listBatches()
    {
        // For MySQL/Postgres compatibility with boolean sums
        $batches = MoneyTransactionExcelBatch::selectRaw('
            batch_no,
            MAX(created_at) as created_at,
            COUNT(*) as total,
            SUM(CASE WHEN is_valid THEN 1 ELSE 0 END) as valid,
            SUM(CASE WHEN is_valid THEN 0 ELSE 1 END) as invalid
        ')
            ->groupBy('batch_no')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $batches]);
    }

    public function preview($batchNo)
    {
        $records = MoneyTransactionExcelBatch::where('batch_no', $batchNo)->get();

        if ($records->isEmpty()) {
            return response()->json(['message' => 'Batch not found.'], 404);
        }

        return response()->json([
            'data' => $records,
            'summary' => [
                'total' => $records->count(),
                'valid' => $records->where('is_valid', true)->count(),
                'invalid' => $records->where('is_valid', false)->count(),
            ]
        ]);
    }

    public function accept($batchNo)
    {
        $records = MoneyTransactionExcelBatch::where('batch_no', $batchNo)
            ->where('is_valid', true)
            ->get();

        if ($records->isEmpty()) {
            return response()->json(['message' => 'No valid records to import.'], 400);
        }

        $importedCount = 0;

        try {
            \DB::beginTransaction();

            // Cache models to minimize queries
            $accounts = MoneyAccount::all()->keyBy('name');
            $categories = MoneyCategory::all()->keyBy('name');
            $subcategories = MoneySubCategory::all()->keyBy('name');

            foreach ($records as $record) {
                $account = $accounts->get($record->money_account_name);
                $category = $categories->get($record->money_category_name);
                $subcategory = $record->money_subcategory_name ? $subcategories->get($record->money_subcategory_name) : null;

                if ($account && $category) {
                    $transaction = new MoneyTransaction();
                    $transaction->amount = intval(round($record->amount * 100));
                    $transaction->transaction_date = $record->transaction_date;
                    $transaction->description = $record->description;
                    $transaction->money_category_id = $category->id;
                    $transaction->money_subcategory_id = $subcategory ? $subcategory->id : null;
                    $transaction->money_account_id = $account->id;
                    $transaction->type = $record->type;
                    $transaction->user_id = $record->user_id;
                    $transaction->save();

                    // Trigger balance recalculation
                    \DB::afterCommit(function () use ($account, $transaction) {
                        \HafizRuslan\Finance\app\Jobs\ProcessAccountBalance::dispatch(
                            $account->id,
                            $transaction->getTransactionDateInYmd()
                        );
                    });

                    $importedCount++;
                }
            }

            // Delete batch after successful import
            MoneyTransactionExcelBatch::where('batch_no', $batchNo)->delete();

            \DB::commit();

            return response()->json([
                'message' => "Successfully imported {$importedCount} transactions.",
                'imported_count' => $importedCount
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }

    public function reject($batchNo)
    {
        $deleted = MoneyTransactionExcelBatch::where('batch_no', $batchNo)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Batch rejected successfully.']);
        }

        return response()->json(['message' => 'Batch not found.'], 404);
    }

    public function updateRecord(Request $request, $batchNo, $id)
    {
        $record = MoneyTransactionExcelBatch::where('batch_no', $batchNo)->findOrFail($id);

        $amountInput = $request->input('amount');
        $record->amount = ($amountInput === '' || $amountInput === null) ? null : floatval(str_replace(',', '', $amountInput));
        $record->transaction_date = \Carbon\Carbon::parse($request->input('transaction_date'))->format('Y-m-d');
        $record->description = $request->input('description');
        $record->type = $request->input('type');
        $record->money_account_name = $request->input('money_account_name');
        $record->money_category_name = $request->input('money_category_name');
        $record->money_subcategory_name = $request->input('money_subcategory_name');

        $record->is_valid = true;
        $record->error_message = null;
        $record->save();

        // Revalidate the single record
        $this->validateRecordAgainstDatabase($record);

        return response()->json([
            'message' => 'Record updated and revalidated.',
            'data' => $record
        ]);
    }

    private function validateRecordAgainstDatabase($record)
    {
        $accounts = MoneyAccount::pluck('name')->map(function ($name) {
            return strtolower($name);
        })->toArray();
        $categories = MoneyCategory::pluck('name')->map(function ($name) {
            return strtolower($name);
        })->toArray();
        $subcategories = MoneySubCategory::pluck('name')->map(function ($name) {
            return strtolower($name);
        })->toArray();

        $errors = [];

        if (!in_array(strtolower($record->money_account_name), $accounts)) {
            $errors[] = "Account '{$record->money_account_name}' not found.";
        }
        if (!in_array(strtolower($record->money_category_name), $categories)) {
            $errors[] = "Category '{$record->money_category_name}' not found.";
        }
        if ($record->money_subcategory_name && !in_array(strtolower($record->money_subcategory_name), $subcategories)) {
            $errors[] = "Subcategory '{$record->money_subcategory_name}' not found.";
        }
        if (!$record->transaction_date) {
            $errors[] = "Invalid date format.";
        }
        if (!$record->amount || !is_numeric($record->amount)) {
            $errors[] = "Invalid amount.";
        }

        if (!empty($errors)) {
            $record->is_valid = false;
            $record->error_message = implode(' ', $errors);
            $record->save();
        } else {
            $record->is_valid = true;
            $record->error_message = null;
            $record->save();
        }
    }

    private function validateBatchAgainstDatabase($batchNo)
    {
        $records = MoneyTransactionExcelBatch::where('batch_no', $batchNo)->get();

        $accounts = MoneyAccount::pluck('name')->map(function ($name) {
            return strtolower($name);
        })->toArray();
        $categories = MoneyCategory::pluck('name')->map(function ($name) {
            return strtolower($name);
        })->toArray();
        $subcategories = MoneySubCategory::pluck('name')->map(function ($name) {
            return strtolower($name);
        })->toArray();

        foreach ($records as $record) {
            if (!$record->is_valid) continue;

            $errors = [];

            if (!in_array(strtolower($record->money_account_name), $accounts)) {
                $errors[] = "Account '{$record->money_account_name}' not found.";
            }
            if (!in_array(strtolower($record->money_category_name), $categories)) {
                $errors[] = "Category '{$record->money_category_name}' not found.";
            }
            if ($record->money_subcategory_name && !in_array(strtolower($record->money_subcategory_name), $subcategories)) {
                $errors[] = "Subcategory '{$record->money_subcategory_name}' not found.";
            }

            if (!empty($errors)) {
                $record->is_valid = false;
                $record->error_message = ($record->error_message ? $record->error_message . ' ' : '') . implode(' ', $errors);
                $record->save();
            }
        }
    }
}
