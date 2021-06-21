<?php

namespace App\Caches;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

final class CurrenciesCache
{
    /**
     * 24h
     *
     * @var int
     */
    private const TTL = 86_400;

    public function __construct(private array $currencies)
    {
    }

    public function get(): Collection
    {
        return Cache::remember($this->key(), self::TTL, function () {
            return Currency::whereIn('symbol', $this->currencies)->get();
        });
    }

    private function key(): string
    {
        return 'currencies-'.implode('_', $this->currencies);
    }
}
