<?php

namespace App\Helpers;

final class CurrencyHelper
{
    public function __construct(private float $value)
    {
    }

    public function format(): string
    {
        if ($this->value >= 1000) {
            return number_format($this->value, 2);
        }

        return (string) $this->value;
    }
}
