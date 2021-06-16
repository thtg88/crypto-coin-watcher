<?php

namespace App\Actions;

use App\Caches\CurrenciesCache;
use App\Models\Coin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

final class CalculatePeriodAveragesAction
{
    public function __construct(
        private Carbon $end_date,
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
                $this->end_date,
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
