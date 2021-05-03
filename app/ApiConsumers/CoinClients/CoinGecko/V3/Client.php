<?php

namespace App\ApiConsumers\CoinClients\CoinGecko\V3;

use App\ApiConsumers\CoinClients\AbstractClient;
use Illuminate\Support\Collection;

final class Client extends AbstractClient
{
    /** @var string */
    private const LIVE_BASE_URL = 'https://api.coingecko.com/api/v3';

    /**
     * The request timeout in seconds.
     *
     * @var int
     */
    private const REQUEST_TIMEOUT = 30;

    public function getBaseUrl(): string
    {
        return self::LIVE_BASE_URL;
    }

    public function coinPrices(string $id, array $currencies): object
    {
        $url = "/simple/price?ids={$id}".
            "&vs_currencies=" . implode(',', $currencies) .
            "&include_last_updated_at=true";

        return $this->http_client->timeout(self::REQUEST_TIMEOUT)
            ->get($this->getBaseUrl() . $url)
            ->throw()
            ->object();
    }

    public function listCoins(): Collection
    {
        $url = '/coins/list?include_platform=false';

        return $this->http_client->timeout(self::REQUEST_TIMEOUT)
            ->get($this->getBaseUrl() . $url)
            ->collect();
    }
}
