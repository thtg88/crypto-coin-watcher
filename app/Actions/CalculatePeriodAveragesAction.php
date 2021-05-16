<?php

namespace App\Actions;

use App\Cache\CurrenciesCache;
use App\Models\Coin;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

final class CalculatePeriodAveragesAction
{
    public function __construct(
        private Coin $coin,
        private array $currencies,
        private int $value,
        private string $period,
    ) {
    }

    public function __invoke(): void
    {
        foreach ($this->currencies() as $currency) {
            $action = new CalculatePeriodCurrencyAverageAction(
                $this->coin,
                $currency,
                $this->value,
                $this->period,
            );

            $action();
        }
    }

    private function currencies(): Collection
    {
        return (new CurrenciesCache($this->currencies))->get();
    }
}
