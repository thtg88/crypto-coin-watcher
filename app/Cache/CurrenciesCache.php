<?php

namespace App\Cache;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

final class CurrenciesCache
{
    /** @var int */
    private const TTL = 120;

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
