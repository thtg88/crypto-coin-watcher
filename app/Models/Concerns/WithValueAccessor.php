<?php

namespace App\Models\Concerns;

trait WithValueAccessor
{
    public function getValueAttribute(float $value): float
    {
        return $value / 100_000_000;
    }
}
