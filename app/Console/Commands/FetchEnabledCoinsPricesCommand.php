<?php

namespace App\Console\Commands;

use App\Jobs\FetchCoinPriceJob;
use App\Models\Coin;
use App\Models\Currency;
use Illuminate\Console\Command;

class FetchEnabledCoinsPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enabled-coins:fetch-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the prices of all enabled coins';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $coins = Coin::enabled()->pluck('external_id');

        $this->info("Fetching {$coins->count()} new coin prices!");

        $currencies = Currency::pluck('symbol')->toArray();

        foreach ($coins as $coin) {
            $this->info("Fetching {$coin->external_id} price...");

            dispatch(new FetchCoinPriceJob($coin->external_id, $currencies));
        }

        return 0;
    }
}
