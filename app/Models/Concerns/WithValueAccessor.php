<?php

namespace App\Models\Concerns;

trait WithValueAccessor
{
    public function getValueAttribute(float $value): float
    {
        return $value / config('app.coin_price_storage_coefficient');
    }
}
