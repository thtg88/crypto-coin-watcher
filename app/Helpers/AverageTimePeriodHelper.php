<?php

namespace App\Helpers;

final class AverageTimePeriodHelper
{
    public static function getMap(): array
    {
        $time_periods_map = [];

        foreach (config('app.averages.time_periods') as $time_period) {
            [$quantity, $period] = explode(' ', $time_period);

            $time_periods_map[$period] = $quantity;
        }

        return $time_periods_map;
    }
}
