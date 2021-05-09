<?php

namespace App\Actions;

use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\Price;
use Illuminate\Database\Eloquent\Collection;

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
            'from' => $this->from(),
            'to' => $this->to(),
            'value' => $this->average(),
        ]);
    }

    private function average(): Collection
    {
        return Price::average('value')
            ->where('currency_id', $this->currency->id)
            ->where('coin_id', $this->coin->id)
            ->between('value_last_updated_at', [$this->from(), ])
            ->get();
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
}
