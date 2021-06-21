<?php

namespace App\Actions;

use App\Caches\ThresholdAlertNotificationCache;
use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\ThresholdAlert;
use App\Notifications\ThresholdAlertNotification;

final class SendThresholdAlertAction
{
    public function __construct(
        private ThresholdAlert $alert,
        private Average $average,
    ) {
    }

    public function __invoke(): void
    {
        $cache = new ThresholdAlertNotificationCache(
            $this->alert->user_id,
            $this->coin()->external_id,
            $this->currency()->symbol,
            $this->alert->trend,
            config('app.cache_ttls.threshold_alert_notification'),
        );
        if ($cache->has()) {
            return;
        }

        $this->alert->user->notify(new ThresholdAlertNotification(
            $this->coin()->external_id,
            $this->currency()->symbol,
            $this->alert->value,
            $this->alert->trend,
            $this->average->value,
        ));

        $cache->get();
    }

    private function coin(): Coin
    {
        return $this->average->coin;
    }

    private function currency(): Currency
    {
        return $this->average->currency;
    }
}
