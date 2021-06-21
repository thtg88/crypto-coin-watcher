<?php

namespace App\Caches;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class VariationPercentageNotificationCache
{
    public function __construct(
        private int $user_id,
        private string $coin_external_id,
        private string $currency_symbol,
        private string $period,
        private int $ttl,
    ) {
    }

    public function has(): bool
    {
        return Cache::has($this->key());
    }

    public function get(): bool
    {
        return Cache::remember($this->key(), $this->ttl, function () {
            return true;
        });
    }

    public function key(): string
    {
        return implode('-', [
            'variation_percentage_notifications',
            'user_'.$this->user_id,
            'coin_'.str_replace('-', '_', $this->coin_external_id),
            'currency_'.$this->currency_symbol,
            'period_'.Str::snake($this->period),
        ]);
    }
}
