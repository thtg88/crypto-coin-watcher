<?php

namespace App\Helpers;

final class TrendHelper
{
    public function __construct(private bool $trend)
    {
    }

    public function format(): string
    {
        if ($this->trend === true) {
            return '✅';
        }

        return '🛑';
    }
}
