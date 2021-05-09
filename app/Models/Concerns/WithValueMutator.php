<?php

namespace App\Models\Concerns;

trait WithValueMutator
{
    public function setValueAttribute(float $value): void
    {
        $this->attributes['value'] = (int) ($value * 100_000_000);
    }
}
