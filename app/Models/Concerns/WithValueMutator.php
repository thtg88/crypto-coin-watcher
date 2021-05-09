<?php

namespace App\Models\Concerns;

trait WithValueMutator
{
    public function setValueAttribute(float $value): void
    {
        $this->attributes['value'] = (int) ($value * 1_000_000);
    }
}
