<?php

namespace HafizRuslan\Finance\app\Console\Commands;

use HafizRuslan\Finance\app\Jobs\ProcessAccountBalance;
use HafizRuslan\Finance\app\Models\MoneyAccount;
use Illuminate\Console\Command;

class DispatchDailyBalanceJob extends Command
{
    protected $signature = 'finance-balance:daily {date? : The date for balance calculation (Y-m-d)}';
    protected $description = 'Dispatch daily account balance calculation for all accounts';

    public function handle()
    {
        $date = $this->argument('date') ?? \Carbon\Carbon::now()->toDateString();

        // Validate format (optional but recommended)
        if (!\Carbon\Carbon::hasFormat($date, 'Y-m-d')) {
            $this->error('Invalid date format. Please use Y-m-d (e.g., 2025-08-26).');
            return Command::FAILURE;
        }

        $this->info("START Dispatch balance job for all accounts on {$date}.");

        MoneyAccount::cursor()->each(function ($account) use ($date) {
            ProcessAccountBalance::dispatch($account->id, $date);
        });

        $this->info("END Dispatched balance job for all accounts on {$date}.");
    }
}
