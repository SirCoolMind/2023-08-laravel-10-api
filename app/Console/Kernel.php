<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('backup:run --only-db')->daily()->at('01:00')
            ->onFailure(function () {
                \Log::error('Command backup:run failed at '.\Carbon\Carbon::now()->toDateTimeString());
            })
            ->onSuccess(function () {
                \Log::info('Command backup:run success at '.\Carbon\Carbon::now()->toDateTimeString());
            });
        $schedule->command('backup:clean')->daily()->at('01:30')
            ->onFailure(function () {
                \Log::error('Command backup:clean failed at '.\Carbon\Carbon::now()->toDateTimeString());
            })
            ->onSuccess(function () {
                \Log::info('Command backup:clean success at '.\Carbon\Carbon::now()->toDateTimeString());
            });

        if (array_key_exists('finance-balance:daily', \Artisan::all())) {
            $schedule->command('finance-balance:daily')->daily()->at('01:00')
                ->onFailure(function () {
                    \Log::error('Command finance-balance:daily failed at '.\Carbon\Carbon::now()->toDateTimeString());
                })
                ->onSuccess(function () {
                    \Log::info('Command finance-balance:daily success at '.\Carbon\Carbon::now()->toDateTimeString());
                });
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
