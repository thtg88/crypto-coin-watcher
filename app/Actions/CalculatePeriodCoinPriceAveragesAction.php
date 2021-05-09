<?php

namespace App\Actions;

use App\Models\Coin;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

final class CalculatePeriodCoinPriceAveragesAction
{
    public function __construct(
        private Coin $coin,
        private int $value,
        private string $period,
    ) {
    }

    public function __invoke(): void
    {
        foreach ($this->getCurrencies() as $currency) {
            $action = new CalculatePeriodCoinPriceCurrencyAverageAction(
                $this->coin,
                $currency,
                $this->value,
                $this->period,
            );

            $action();
        }
    }

    private function getCurrencies(): Collection
    {
        return Currency::whereIn('symbol', $this->currencies)->get();
    }
}
