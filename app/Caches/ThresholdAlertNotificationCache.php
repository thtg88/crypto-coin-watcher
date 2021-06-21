<?php

namespace App\Caches;

use Illuminate\Support\Facades\Cache;

final class ThresholdAlertNotificationCache
{
    public function __construct(
        private int $user_id,
        private string $coin_external_id,
        private string $currency_symbol,
        private bool $trend,
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

    public function delete(): bool
    {
        return Cache::forget($this->key());
    }

    public function key(): string
    {
        return implode('-', [
            'threshold_alert_notifications',
            'user_'.$this->user_id,
            'coin_'.str_replace('-', '_', $this->coin_external_id),
            'currency_'.$this->currency_symbol,
            'trend_'.($this->trend === true ? 'true' : 'false'),
        ]);
    }
}
