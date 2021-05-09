<?php

namespace App\Console\Commands;

use App\Jobs\FetchAllCoinsJob;
use App\Jobs\FetchCoinPriceJob;
use App\Models\Coin;
use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class HorizonRefillQueuesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'horizon:refill-delayed-queues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refill Horizon queues if empty.';

    public function handle(): int
    {
        $delayed_jobs = Redis::zrange('queues:default:delayed', 0, -1);
        dd($delayed_jobs);
        if (!empty($delayed_jobs)) {
            $this->warn("Default delayed queue is not empty, skipping.");

            return 0;
        }

        $this->warn("Dispatching ".FetchAllCoinsJob::class."...");
        dispatch(new FetchAllCoinsJob());
        $this->info("Dispatched ".FetchAllCoinsJob::class);

        $currencies = Currency::pluck('symbol')->toArray();
        $coins = Coin::enabled()->pluck('external_id')->toArray();

        foreach ($coins as $coin) {
            $this->warn(
                "Dispatching ".FetchAllCoinsJob::class.
                " for {$coin} and ".implode(',', $currencies)."..."
            );
            dispatch(new FetchCoinPriceJob($coin, $currencies));
            $this->info(
                "Dispatched ".FetchAllCoinsJob::class.
                " for {$coin} and ".implode(',', $currencies)
            );
        }

        return 0;
    }
}
