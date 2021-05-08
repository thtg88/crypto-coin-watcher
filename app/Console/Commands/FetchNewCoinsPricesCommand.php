<?php

namespace App\Console\Commands;

use App\Jobs\FetchCoinPriceJob;
use App\Models\Coin;
use App\Models\Currency;
use Illuminate\Console\Command;

class FetchNewCoinsPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coin-prices:fetch-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the prices of coin that have never been fetched';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $coin_without_prices = Coin::doesntHave('prices')->enabled()->pluck('external_id');
        if ($coin_without_prices->isEmpty()) {
            $this->warn('No new coin prices to fetch.');

            return 0;
        }

        $this->info("Fetching {$coin_without_prices->count()} new coin prices!");

        $currencies = Currency::select('symbol')->pluck('symbol')->toArray();

        foreach ($coin_without_prices as $coin) {
            $this->info("Fetching {$coin->external_id} price...");

            dispatch(new FetchCoinPriceJob($coin->external_id, $currencies));
        }

        return 0;
    }
}
