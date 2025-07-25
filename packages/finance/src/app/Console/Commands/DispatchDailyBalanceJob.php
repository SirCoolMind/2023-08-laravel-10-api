<?php

namespace HafizRuslan\Finance\app\Console\Commands;

use HafizRuslan\Finance\app\Jobs\ProcessAccountBalance;
use HafizRuslan\Finance\app\Models\MoneyAccount;
use Illuminate\Console\Command;

class DispatchDailyBalanceJob extends Command
{
    protected $signature = 'finance-balance:daily';
    protected $description = 'Dispatch daily account balance calculation for all accounts';

    public function handle()
    {
        $today = \Carbon\Carbon::now()->toDateString();

        $this->info("START Dispatch balance job for all accounts on {$today}.");

        MoneyAccount::cursor()->each(function ($account) use ($today) {
            ProcessAccountBalance::dispatch($account->id, $today);
        });

        $this->info("END Dispatched balance job for all accounts on {$today}.");
    }
}
