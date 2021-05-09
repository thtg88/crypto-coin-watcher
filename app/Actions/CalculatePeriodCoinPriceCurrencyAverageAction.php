<?php

namespace App\Actions;

use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\Price;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class CalculatePeriodCoinPriceCurrencyAverageAction
{
    private Carbon $from;
    private Carbon $to;

    public function __construct(
        private Coin $coin,
        private Currency $currency,
        private int $value,
        private string $period,
    ) {
    }

    public function __invoke(): void
    {
        Average::firstOrCreate([
            'coin_id' => $this->coin->id,
            'currency_id' => $this->currency->id,
            'period' => $this->getFullPeriod(),
            'from' => $this->from(),
            'to' => $this->to(),
        ], ['value' => $this->average()]);
    }

    private function average(): float
    {
        return (float) Price::query()
            ->where('currency_id', $this->currency->id)
            ->where('coin_id', $this->coin->id)
            ->whereBetween('value_last_updated_at', [
                $this->from(),
                $this->to(),
            ])
            ->average('value');
    }

    private function getCarbonMethod(): string
    {
        return 'sub'.Str::title($this->period);
    }

    private function from(): Carbon
    {
        $method = $this->getCarbonMethod();

        $this->from ??= now()->$method($this->value);

        return $this->from;
    }

    private function to(): Carbon
    {
        $this->to ??= now();

        return $this->to;
    }

    private function getFullPeriod(): string
    {
        return ((string) $this->value).$this->period;
    }
}
