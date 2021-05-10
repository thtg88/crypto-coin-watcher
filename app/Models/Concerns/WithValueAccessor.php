<?php

namespace App\Models\Concerns;

trait WithValueAccessor
{
    public function getValueAttribute(float $value): float
    {
        return $value / config('app.coin_storage_coefficient');
    }
}
