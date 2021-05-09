<?php

namespace App\Jobs;

use App\ApiConsumers\CoinClients\CoinGecko\V3\Client;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\Price;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class FetchCoinPriceJob extends ScheduledJob
{
    private bool $succeeded = false;

    public function __construct(
        private string $coin_external_id,
        private array $currencies,
    ) {
    }

    public function handle(): void
    {
        $coin = $this->coin();
        // If coin has been deleted, early return
        if ($coin === null) {
            return;
        }

        // If fetching price fail, the queue will deal with it
        $coin_prices_data = $this->coinPrices($coin);

        $external_id = $this->coin_external_id;
        $coin_price_data = $coin_prices_data->$external_id;

        if ($coin->getLastPriceUpdatedAtTimestamp() === $coin_price_data->last_updated_at) {
            return;
        }

        foreach ($this->getAllCurrencies() as $currency) {
            $this->createPrice($coin, $currency, $coin_price_data);
        }

        $this->succeeded = true;
    }

    protected function nextExecutesAt(): ?Carbon
    {
        // If job failed, don't reschedule, let the queue handle it
        if ($this->succeeded === false) {
            return null;
        }

        return now()->addSeconds(
            config('app.scheduled_jobs.fetch_coin_price_frequency')
        );
    }

    protected function getNextArgs(): array
    {
        return [$this->coin_external_id, $this->currencies];
    }

    private function coin(): ?Coin
    {
        return Coin::withLastPriceId()->with('lastPrice')
            ->firstWhere('external_id', $this->coin_external_id);
    }

    private function coinPrices($coin): object
    {
        return Client::make()->coinPrices(
            $coin->external_id,
            $this->currencies
        );
    }

    private function getAllCurrencies(): Collection
    {
        return Currency::whereIn('symbol', $this->currencies)->get();
    }

    private function createPrice(
        Coin $coin,
        Currency $currency,
        object $price_data,
    ): Price {
        $symbol = $currency->symbol;

        return $coin->prices()->create([
            'currency_id' => $currency->id,
            'value' => $price_data->$symbol,
            'value_last_updated_at' => Carbon::parse(
                $price_data->last_updated_at
            ),
        ]);
    }
}
