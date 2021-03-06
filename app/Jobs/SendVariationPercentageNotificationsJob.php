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
            return;
        }

        // Do not bother notifying if difference is:
        // - 0 <= variation_percentage < 5
        // - -5 > variation_percentage >= 0
        if (abs($variation_percentage) < self::PERCENTAGE_THRESHOLD) {
            return;
        }

        foreach ($this->alerts($variation_percentage) as $alert) {
            $action = new SendVariationPercentageNotificationAction(
                $alert,
                $this->coin(),
                $this->currency(),
                $this->average,
                $variation_percentage,
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
            ->where('to', '>=', $this->startingAverageDateTime())
            ->where('coin_id', $this->average->coin_id)
            ->where('currency_id', $this->average->currency_id)
            ->where('period', $this->average->period)
            ->first();

        return $this->startingAverage;
    }

    private function startingAverageDateTime(): string
    {
        return $this->average->from
            ->subSeconds(config('app.cache_ttls.variation_percentage_notification'))
            ->toDateTimeString();
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
