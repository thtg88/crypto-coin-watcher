<?php

namespace App\Jobs;

use App\Actions\SendVariationPercentageNotificationAction;
use App\Helpers\AverageVariationHelper;
use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\VariationPercentageAlert;
use DivisionByZeroError;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class SendVariationPercentageNotificationsJob extends Job
{
    /** @var string */
    public const PROCESSABLE_PERIOD = '1 hours';

    /** @var float */
    private const PERCENTAGE_THRESHOLD = 5.0;

    private Coin $coin;
    private Currency $currency;
    private Average $startingAverage;

    public function __construct(private Average $average)
    {
    }

    public function handle(): void
    {
        if ($this->average->period !== self::PROCESSABLE_PERIOD) {
            return;
        }

        // If any of the values invalid, do not process
        try {
            $variation_percentage = $this->variationPercentage();
        } catch (DivisionByZeroError) {
            Log::debug('division by zero, startingAverage: '.$this->startingAverage());
            Log::debug('division by zero, average: '.$this->average);
            return;
        }

        Log::debug("coin={$this->coin()->external_id} variation_percentage={$variation_percentage}");

        // Do not bother notifying if difference is:
        // - 0 <= variation_percentage < 5
        // - -5 > variation_percentage >= 0
        if (abs($variation_percentage) < self::PERCENTAGE_THRESHOLD) {
            Log::debug("variation too little, exiting");
            return;
        }

        $alerts = $this->alerts($variation_percentage);

        Log::debug("alerts JSON: {$alerts->toJson()}");

        foreach ($alerts as $alert) {
            Log::debug("Processing alert: {$alert->toJson()}");

            $action = new SendVariationPercentageNotificationAction(
                $alert,
                $this->coin(),
                $this->currency(),
                $variation_percentage,
                $this->average->period,
            );

            $action();
        }
    }

    private function variationPercentage(): float
    {
        return AverageVariationHelper::fromModels(
            $this->startingAverage(),
            $this->average,
        )->percentage();
    }

    private function startingAverage(): ?Average
    {
        $this->startingAverage ??= Average::orderBy('to')
            ->where('to', '>=', $this->average->from->subHours(1))
            ->where('coin_id', $this->average->coin_id)
            ->where('currency_id', $this->average->currency_id)
            ->where('period', $this->average->period)
            ->first();

        return $this->startingAverage;
    }

    private function alerts(float $variation_percentage): Collection
    {
        // if variation is -10% and my alert is for -5%
        // I want to get notified
        if ($variation_percentage < 0) {
            return $this->baseQuery()->whereBetween('threshold', [
                $variation_percentage,
                -1 * self::PERCENTAGE_THRESHOLD,
            ])->get();
        }

        // if variation is 10% and my alert is for 5%
        // I want to get notified
        return $this->baseQuery()->whereBetween('threshold', [
            self::PERCENTAGE_THRESHOLD,
            $variation_percentage,
        ])->get();
    }

    private function baseQuery(): Builder
    {
        return VariationPercentageAlert::with('user')
            ->where('coin_id', $this->average->coin_id)
            ->where('currency_id', $this->average->currency_id)
            ->where('period', $this->average->period);
    }

    private function coin(): Coin
    {
        $this->coin ??= $this->average->coin;

        return $this->coin;
    }

    private function currency(): Currency
    {
        $this->currency ??= $this->average->currency;

        return $this->currency;
    }
}
