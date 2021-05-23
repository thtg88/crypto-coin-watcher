<?php

namespace App\Providers;

use App\ApiConsumers\CoinClients\CoinGecko\V3\Client;
use Illuminate\Support\ServiceProvider;

class CoinGeckoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            return Client::make();
        });
    }
}
