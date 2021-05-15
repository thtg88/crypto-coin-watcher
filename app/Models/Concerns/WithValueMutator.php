<?php

namespace App\Models\Concerns;

trait WithValueMutator
{
    public function setValueAttribute(float $value): void
    {
        $this->attributes['value'] = (int) (
            $value * config('app.coin_price_storage_coefficient')
        );
    }
}
