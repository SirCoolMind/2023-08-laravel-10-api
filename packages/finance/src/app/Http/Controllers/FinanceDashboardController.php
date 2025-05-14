<?php

namespace HafizRuslan\Finance\app\Http\Controllers;

use HafizRuslan\Finance\app\Http\Resources\MoneyTransactionV2Resource;
use HafizRuslan\Finance\app\Models\MoneyTransaction;

class FinanceDashboardController extends \App\Http\Controllers\Controller
{
    public function transactionListing()
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        \Log::debug(request()->all());
        // * sort
        $sortBy = 'transaction_date';
        $descending = 'DESC';
        $search = request()->input('filter.search');

        $startDate = request()->input('filter.start_date');
        $endDate = request()->input('filter.end_date');
        if (!$startDate) {
            \Log::debug("masuk sini");
            $startDate = \Carbon\Carbon::now()->startOfMonth();
            $endDate = $startDate->clone()->endOfMonth();
        }

        \Log::debug($startDate);

        // * fetch
        $records = MoneyTransaction::with($this->withRelations())
            ->orderBy($sortBy, $descending)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('transaction_date', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('transaction_date', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
            })
            ->get();

        $totalIncome = $records->groupBy('type')->map(function ($items) {
            return $items->sum('amount');
        });

        $totals = $records->groupBy('type')->map(fn ($items) => $items->sum('amount'))->toArray();
        // Ensure both keys exist with a default value of 0.00
        $totals = array_merge(['EXPENSE' => 0.00, 'INCOME' => 0.00], $totals);

        $totalExpense = number_format($totals['EXPENSE'] / 100, 2, '.', '');
        $totalIncome = number_format($totals['INCOME'] /100, 2, '.', '');

        return MoneyTransactionV2Resource::collection($records)
            ->additional([
                'total_expense' => $totalExpense,
                'total_income'  => $totalIncome,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
            ]);
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
