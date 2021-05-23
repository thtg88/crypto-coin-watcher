<?php

namespace App\Jobs;

use App\ApiConsumers\CoinClients\CoinGecko\V3\Client;
use App\Models\Coin;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class FetchAllCoinsJob extends Job
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    public function handle(): void
    {
        $enabled_coin_ids = config('app.enabled_coins');

        foreach ($this->fetchCoins() as $coin) {
            if (!in_array($this->getExternalId($coin), $enabled_coin_ids)) {
                continue;
            }

            $this->updateOrCreate($coin);
        }
    }

    private function fetchCoins(): Collection
    {
        return app(Client::class)->listCoins();
    }

    private function updateOrCreate(array $coin): Coin
    {
        return Coin::updateOrCreate(
            ['external_id' => $this->getExternalId($coin)],
            $this->getData($coin)
        );
    }

    private function getExternalId(array $coin): string
    {
        return $coin['id'];
    }

    private function getData(array $coin): array
    {
        return [
            'name' => $coin['name'],
            'symbol' => $coin['symbol'],
        ];
    }
}
