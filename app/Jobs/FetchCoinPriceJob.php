<?php

namespace App\Jobs;

use App\ApiConsumers\CoinClients\CoinGecko\V3\Client;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\Price;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class FetchCoinPriceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Client $coin_gecko_client;

    public function __construct(
        private string $coin_external_id,
        private array $currencies,
    ) {
        $this->coin_gecko_client = Client::make();
    }

    public function handle(): void
    {
        $coin = $this->coin();

        try {
            $coin_prices_data = $this->coinPrices($coin);
        } catch (Exception) {
            $this->redispatchAt(now()->addDay());

            return;
        }

        $external_id = $this->coin_external_id;
        $coin_price_data = $coin_prices_data->$external_id;

        if ($coin->getLastPriceUpdatedAtTimestamp() === $coin_price_data->last_updated_at) {
            return;
        }

        foreach ($this->getAllCurrencies() as $currency) {
            $this->createPrice($coin, $currency, $coin_price_data);
        }

        $this->redispatchAt(now()->addMinutes(10));
    }

    public function redispatchAt(Carbon $date): void
    {
        self::dispatchAt($this->coin_external_id, $this->currencies, $date);
    }

    public static function dispatchAt(
        string $coin_external_id,
        array $currencies,
        Carbon $date,
    ): void {
        self::dispatch($coin_external_id, $currencies)->delay($date);
    }

    private function coin(): Coin
    {
        return Coin::withLastPriceId()->with('lastPrice')
            ->firstWhere('external_id', $this->coin_external_id);
    }

    private function coinPrices($coin): object
    {
        return $this->coin_gecko_client
            ->coinPrices($coin->external_id, $this->currencies);
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
