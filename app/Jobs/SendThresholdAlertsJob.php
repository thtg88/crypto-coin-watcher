<?php

namespace App\Jobs;

use App\Actions\SendThresholdAlertAction;
use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\ThresholdAlert;
use Illuminate\Database\Eloquent\Collection;

class SendThresholdAlertsJob extends Job
{
    public const PROCESSABLE_PERIOD = '1 hours';

    public function __construct(private Average $average)
    {
    }

    public function handle(): void
    {
        foreach ($this->alerts() as $alert) {
            $action = new SendThresholdAlertAction($alert, $this->average);

            $action();
        }
    }

    private function coin(): Coin
    {
        return $this->average->coin;
    }

    private function currency(): Currency
    {
        return $this->average->currency;
    }

    private function alerts(): Collection
    {
        return ThresholdAlert::query()
            ->with('user')
            ->where('coin_id', $this->coin()->id)
            ->where('currency_id', $this->currency()->id)
            ->where(function ($query) {
                // If trend is rising/increasing,
                // we want all the alerts with lower values
                // OTHERWISE (OR)
                // Otherwise if trend is falling/decreasing,
                // we want all the alerts with higher values
                $query->orWhere(function ($query) {
                    $query->where('trend', true)->where(
                        'value',
                        '<=',
                        $this->average->value * config(
                            'app.coin_price_storage_coefficient'
                        )
                    );
                })->orWhere(function ($query) {
                    $query->where('trend', false)->where(
                        'value',
                        '>=',
                        $this->average->value * config(
                            'app.coin_price_storage_coefficient'
                        )
                    );
                });
            })
            ->get();
    }
}
