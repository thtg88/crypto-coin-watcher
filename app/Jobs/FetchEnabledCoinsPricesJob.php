<?php

namespace App\Jobs;

use App\Models\Currency;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class FetchEnabledCoinsPricesJob extends Job
{
    public function handle(): void
    {
        $currencies = Currency::pluck('symbol')->toArray();

        foreach (config('app.enabled_coins') as $coin_external_id) {
            dispatch(new FetchCoinPriceJob($coin_external_id, $currencies));
        }
    }
}
