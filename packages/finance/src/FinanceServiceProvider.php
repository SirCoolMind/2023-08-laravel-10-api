<?php

namespace HafizRuslan\Finance;

use HafizRuslan\Finance\app\Console\Commands\DispatchDailyBalanceJob;
use Illuminate\Support\ServiceProvider;

class FinanceServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DispatchDailyBalanceJob::class,
            ]);
        }
    }
}
