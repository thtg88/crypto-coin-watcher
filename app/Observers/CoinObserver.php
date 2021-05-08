<?php

namespace App\Observers;

use App\Jobs\FetchCoinPriceJob;
use App\Models\Coin;
use App\Models\Currency;

class CoinObserver
{
    /**
     * Handle the Coin "created" event.
     *
     * @param \App\Models\Coin $coin
     * @return void
     */
    public function created(Coin $coin): void
    {
        $currencies = Currency::select('symbol')->pluck('symbol')->toArray();

        dispatch(new FetchCoinPriceJob($coin->external_id, $currencies));
    }
}
