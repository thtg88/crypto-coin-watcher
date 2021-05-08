<?php

namespace App\Jobs;

use App\ApiConsumers\CoinClients\CoinGecko\V3\Client;
use App\Models\Coin;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Carbon;

class FetchAllCoinsJob extends ScheduledJob
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    private Client $coin_gecko_client;

    public function __construct()
    {
        $this->coin_gecko_client = Client::make();
    }

    public function handle(): void
    {
        $coins = $this->coin_gecko_client->listCoins();

        foreach ($coins as $coin_data) {
            $this->updateOrCreate($coin_data);
        }
    }

    protected function nextExecutesAt(): Carbon
    {
        return now()->addDay();
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
