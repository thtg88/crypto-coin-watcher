<?php

namespace App\Jobs;

use App\Actions\CalculatePeriodCoinPriceAveragesAction;
use App\Models\Coin;
use Illuminate\Contracts\Queue\ShouldBeUnique;

final class CalculateCoinAveragesJob extends Job
{
    public function __construct(
        private string $coin_external_id,
        private array $currencies,
    ) {
    }

    public function handle(): void
    {
        $coin = $this->coin();

        foreach ($this->getPeriodsMap() as $period => $quantity) {
            $action = new CalculatePeriodCoinPriceAveragesAction(
                $coin,
                $this->currencies,
                $quantity,
                $period,
            );

            $action();
        }
    }

    /** @psalm-suppress InvalidReturnType */
    private function coin(): ?Coin
    {
        /** @psalm-suppress InvalidReturnStatement */
        return Coin::firstWhere('external_id', $this->coin_external_id);
    }

    private function getPeriodsMap(): array
    {
        $time_periods = config('app.average_coin_prices.time_periods');
        $time_periods_map = [];

        foreach ($time_periods as $time_period) {
            [$quantity, $period] = explode(' ', $time_period);

            $time_periods_map[$period] = $quantity;
        }

        return $time_periods_map;
    }
}
