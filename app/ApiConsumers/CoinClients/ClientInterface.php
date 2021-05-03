<?php

namespace App\ApiConsumers\CoinClients;

use Illuminate\Http\Client\Factory;
use Illuminate\Support\Collection;

interface ClientInterface
{
    public static function fake(): self;
    public static function make(): self;
    public function getBaseUrl(): string;
    public function coinPrices(string $id, array $currencies): object;
    public function listCoins(): Collection;
    public function setHttpClient(Factory $http_client): self;
}
