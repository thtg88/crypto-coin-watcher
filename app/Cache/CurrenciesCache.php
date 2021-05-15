<?php

namespace App\Cache;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

final class CurrenciesCache
{
    public function __construct(private array $currencies)
    {
    }

    public function get(): Collection
    {
        return Cache::remember($this->currenciesCacheKey(), 120, function () {
            return Currency::whereIn('symbol', $this->currencies)->get();
        });
    }

    private function currenciesCacheKey(): string
    {
        return 'currencies-'.implode('_', $this->currencies);
    }
}
