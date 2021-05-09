<?php

namespace App\Console;

use App\Jobs\FetchAllCoinsJob;
use App\Jobs\FetchCoinPriceJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new FetchAllCoinsJob())->daily();
        $schedule->job(new FetchEnabledCoinsPricesJob())->everyMinute();
        $schedule->command('horizon:snapshot')->everyMinute();
        $schedule->command('telescope:prune --hours=24')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
