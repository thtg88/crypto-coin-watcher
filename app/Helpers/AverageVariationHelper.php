<?php

namespace App\Helpers;

use App\Models\Average;
use DivisionByZeroError;

final class AverageVariationHelper
{
    public function __construct(private float $first, private float $last)
    {
    }

    public static function fromModels(Average $first, Average $last): self
    {
        return new self($first->value, $last->value);
    }

    public function formattedPercentage(): string
    {
        try {
            return number_format($this->percentage(), 2);
        } catch (DivisionByZeroError) {
            return '-';
        }
    }

    public function percentage(): float
    {
        return $this->rate() * 100;
    }

    /** @throws DivisionByZeroError If the first value is 0 */
    public function rate(): float
    {
        return $this->get() / $this->first;
    }

    public function get(): float
    {
        return $this->last - $this->first;
    }
}
