<?php

namespace App\Console;

use App\Jobs\SendDailyDigestsJob;
use App\Jobs\SendWeeklyDigestsJob;
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
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Every morning at 8am (7am UTC)
        $schedule->job(new SendDailyDigestsJob())->dailyAt('07:00');
        // Every Saturday at 10am (9am UTC)
        $schedule->job(new SendWeeklyDigestsJob())->weeklyOn(6, '09:00');
        $schedule->command('enabled-coins:fetch-prices')->everyFiveMinutes();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        if (config('telescope.enabled') === true) {
            $schedule->command('telescope:prune --hours=24')->everyFiveMinutes();
        }
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
