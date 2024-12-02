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
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
        $schedule->command('backup:run --only-db')->daily()->at('01:00')
            ->onFailure(function () {
                \Log::error("Command backup:run failed at ".\Carbon\Carbon::now()->toDateTimeString());
            })
            ->onSuccess(function () {
                \Log::error("Command backup:run success at ".\Carbon\Carbon::now()->toDateTimeString());
            });
        $schedule->command('backup:clean')->daily()->at('01:30')
            ->onFailure(function () {
                \Log::error("Command backup:clean failed at ".\Carbon\Carbon::now()->toDateTimeString());
            })
            ->onSuccess(function () {
                \Log::error("Command backup:clean success at ".\Carbon\Carbon::now()->toDateTimeString());
            });
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
