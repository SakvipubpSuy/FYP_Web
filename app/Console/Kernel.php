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
        // Schedule the energy:refill command to run daily at 0500 UTC+7
        // $schedule->command('refill:user-energy')->dailyAt('05:00')->timezone('Asia/Bangkok');
        $schedule->command('refill:user-energy');
        // $schedule->command('app:test');
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
