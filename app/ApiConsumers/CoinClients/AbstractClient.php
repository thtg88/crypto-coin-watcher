<?php

namespace App\ApiConsumers\CoinClients;

use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;

abstract class AbstractClient implements ClientInterface
{
    public function __construct(protected Factory $http_client)
    {
    }

    public static function fake(): self
    {
        return new static(Http::fake());
    }

    public static function make(): self
    {
        return new static(new Factory());
    }

    public function setHttpClient(Factory $http_client): self
    {
        $this->http_client = $http_client;

        return $this;
    }
}
