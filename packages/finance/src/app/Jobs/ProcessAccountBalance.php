<?php
namespace HafizRuslan\Finance\app\Jobs;

use HafizRuslan\Finance\app\Enums\FinanceTypeEnum;
use HafizRuslan\Finance\app\Models\MoneyAccount;
use HafizRuslan\Finance\app\Models\MoneyTransaction;
use HafizRuslan\Finance\app\Models\MoneyBalance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ProcessAccountBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $accountId;
    protected Carbon $startDate;

    public function __construct(?int $accountId = null, string $startDate)
    {
        $this->accountId = $accountId;
        $this->startDate = Carbon::parse($startDate)->startOfDay();
    }

    public function handle(): void
    {
        \Log::info("START Finance ProcessAccountBalance");
        \Log::info("startDate:".$this->startDate->toDateString());
        \Log::info("accountId:".$this->accountId);

        $accounts = $this->accountId
        ? collect([MoneyAccount::findOrFail($this->accountId)])
        : MoneyAccount::all();

        foreach ($accounts as $account) {
            $this->setBalance($account);
        }
        \Log::info("END Finance ProcessAccountBalance");
    }

    private function setBalance(MoneyAccount $account)
    {
        // Get all transactions from startDate onward
        $transactions = MoneyTransaction::where('money_account_id', $account->id)
            ->whereDate('transaction_date', '>=', $this->startDate)
            ->orderBy('transaction_date')
            ->get()
            ->groupBy(fn ($txn) => Carbon::parse($txn->transaction_date)->toDateString());

        // Get balance before startDate
        $previousBalance = MoneyTransaction::where('money_account_id', $account->id)
            ->whereDate('transaction_date', '<', $this->startDate)
            ->get()
            ->reduce(function ($carry, $txn) {
                return $carry + ($txn->type === FinanceTypeEnum::INCOME ? $txn->amount : -$txn->amount);
            }, 0);

        $runningBalance = $previousBalance;

        // Process day by day
        $current = $this->startDate->copy();
        $end = now()->endOfDay();

        while ($current <= $end) {
            $dateStr = $current->toDateString();
            $dayTxns = $transactions[$dateStr] ?? collect();

            foreach ($dayTxns as $txn) {
                $runningBalance += ($txn->type === FinanceTypeEnum::INCOME) ? $txn->amount : -$txn->amount;
            }

            // Upsert balance for the day
            MoneyBalance::updateOrCreate(
                [
                    'money_account_id' => $account->id,
                    'transaction_date' => $current,
                ],
                [
                    'balance' => $runningBalance,
                    'user_id' => $account->user_id,
                ]
            );

            $current->addDay();
        }
    }
}
