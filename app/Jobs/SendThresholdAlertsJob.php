<?php

namespace App\Jobs;

use App\Actions\SendThresholdAlertAction;
use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\ThresholdAlert;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class SendThresholdAlertsJob extends Job
{
    public const PROCESSABLE_PERIOD = '1 hours';

    private Average $previousAverage;

    public function __construct(private Average $average)
    {
    }

    public function handle(): void
    {
        $alerts = $this->alerts();

        Log::debug("{$alerts->count()} alerts found for {$this->coin()->external_id}");

        foreach ($alerts as $alert) {
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
        return $this->alertsQuery()->get();
    }

    private function alertsQuery(): Builder
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
            });
    }
}
