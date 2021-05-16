<?php

namespace App\Jobs;

use App\Actions\CalculatePeriodAveragesAction;
use App\Helpers\AverageTimePeriodHelper;
use App\Models\Coin;
use Illuminate\Contracts\Queue\ShouldBeUnique;

final class CalculateAveragesJob extends Job
{
    public function __construct(
        private string $coin_external_id,
        private array $currencies,
    ) {
    }

    public function handle(): void
    {
        $coin = Coin::firstWhere('external_id', $this->coin_external_id);

        foreach (AverageTimePeriodHelper::getMap() as $period => $quantity) {
            $action = new CalculatePeriodAveragesAction(
                $coin,
                $this->currencies,
                $quantity,
                $period,
            );

            $action();
        }
    }
}
