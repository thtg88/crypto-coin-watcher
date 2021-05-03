<?php

namespace App\Jobs;

use App\ApiConsumers\CoinClients\CoinGecko\V3\Client;
use App\Models\Coin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchAllCoinsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Client $coin_gecko_client;

    public function __construct()
    {
        $this->coin_gecko_client = Client::make();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $coins = $this->coin_gecko_client->listCoins();

        foreach ($coins as $coin_data) {
            $this->updateOrCreate($coin_data);
        }

        FetchAllCoinsJob::dispatch()->delay(now()->addDay());
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
