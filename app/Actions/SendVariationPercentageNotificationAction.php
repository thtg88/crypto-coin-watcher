<?php

namespace App\Actions;

use App\Caches\VariationPercentageNotificationCache;
use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\VariationPercentageAlert;
use App\Notifications\VariationPercentageNotification;

final class SendVariationPercentageNotificationAction
{
    public function __construct(
        private VariationPercentageAlert $alert,
        private Coin $coin,
        private Currency $currency,
        private Average $final_average,
        private float $variation_percentage,
    ) {
    }

    public function __invoke(): void
    {
        // Don't re-process if already sent in the past 2 hours
        $cache = new VariationPercentageNotificationCache(
            $this->alert->user_id,
            $this->coin->external_id,
            $this->currency->symbol,
            $this->final_average->period,
            config('app.cache_ttls.variation_percentage_notification'),
        );
        if ($cache->has()) {
            return;
        }

        $this->alert->user->notify(new VariationPercentageNotification(
            $this->final_average->period,
            $this->final_average->value,
            $this->coin->external_id,
            $this->variation_percentage
        ));

        // By get-ting, we store the value in cache for the TTL
        // So that we don't reprocess the notification for the next TTL
        $cache->get();
    }
}
