<?php

namespace App\Console;

use App\Jobs\SendDailyDigestsJob;
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
        // Every morning at 8am (7am UTC)
        $schedule->job(new SendDailyDigestsJob())->dailyAt('07:00');
        // Every Saturday at 10am
        // $schedule->job(new SendWeeklyDigestsJob())->everyMinute(); // ->weeklyOn(6, '10:00');
        $schedule->command('enabled-coins:fetch-prices')->everyTwoMinutes();
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
