<?php

namespace App\Actions;

use App\Caches\VariationPercentageNotificationCache;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\VariationPercentageAlert;
use App\Notifications\VariationPercentageNotification;
use Illuminate\Support\Facades\Log;

final class SendVariationPercentageNotificationAction
{
    public function __construct(
        private VariationPercentageAlert $alert,
        private Coin $coin,
        private Currency $currency,
        private float $variation_percentage,
        private string $period,
    ) {
    }

    public function __invoke(): void
    {
        // Don't re-process if already sent in the past hour
        $cache = new VariationPercentageNotificationCache(
            $this->alert->user_id,
            $this->coin->external_id,
            $this->currency->symbol,
            $this->period,
        );
        if ($cache->has()) {
            return;
        }

        $this->alert->user->notify(new VariationPercentageNotification(
            $this->period,
            $this->coin->external_id,
            $this->variation_percentage
        ));

        // By get-ting, we store the value in cache for the TTL
        // So that we don't reprocess the notification for the next TTL
        $cache->get();
    }
}
