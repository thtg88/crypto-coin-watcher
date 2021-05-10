<?php

namespace App\Actions;

use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\Price;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class CalculatePeriodCoinPriceCurrencyAverageAction
{
    private Carbon $pricesFrom;
    private Carbon $pricesTo;

    public function __construct(
        private Coin $coin,
        private Currency $currency,
        private int $value,
        private string $period,
    ) {
    }

    public function __invoke(): void
    {
        if (!$this->shouldCalculate()) {
            Log::debug(
                "Not enough data to calculate averages ".
                "from {$this->pricesFrom()} to {$this->pricesTo()}"
            );

            return;
        }

        Average::firstOrCreate([
            'coin_id' => $this->coin->id,
            'currency_id' => $this->currency->id,
            'period' => $this->getFullPeriod(),
            'from' => $this->newAverageFrom(),
            'to' => $this->newAverageTo(),
        ], ['value' => $this->average()]);
    }

    private function shouldCalculate(): bool
    {
        return Price::query()->where(
            'value_last_updated_at',
            '<',
            $this->pricesFrom()
        )->exists();
    }

    private function newAverageFrom(): string
    {
        return $this->baseQuery()->min('value_last_updated_at');
    }

    private function newAverageTo(): string
    {
        return $this->baseQuery()->max('value_last_updated_at');
    }

    private function average(): float
    {
        $average = (float) $this->baseQuery()->average('value');

        Log::debug(
            "Average for {$this->getFullPeriod()} ".
            "{$this->coin->external_id}: ".json_encode($average)
        );

        return $average;
    }

    private function baseQuery(): Builder
    {
        return Price::query()
            ->where('currency_id', $this->currency->id)
            ->where('coin_id', $this->coin->id)
            ->whereBetween('value_last_updated_at', [
                $this->pricesFrom(),
                $this->pricesTo(),
            ]);
    }

    private function getCarbonMethod(): string
    {
        return 'sub'.Str::title($this->period);
    }

    private function pricesFrom(): string
    {
        $method = $this->getCarbonMethod();

        $this->pricesFrom ??= now()->$method($this->value);

        return $this->pricesFrom->toDateTimeString();
    }

    private function pricesTo(): string
    {
        $this->pricesTo ??= now();

        return $this->pricesTo->toDateTimeString();
    }

    private function getFullPeriod(): string
    {
        return implode(' ', [(string) $this->value, $this->period]);
    }
}
