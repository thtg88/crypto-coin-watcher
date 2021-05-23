<?php

namespace Tests\Unit;

use App\Helpers\CurrencyHelper;
use PHPUnit\Framework\TestCase;

class CurrencyHelperTest extends TestCase
{
    /** @test */
    public function format_greater_than_one_thousand_returns_two_decimals_only(): void
    {
        $helper = new CurrencyHelper(1234.56789);

        $this->assertEquals('1,234.57', $helper->format());
    }

    /** @test */
    public function format_less_than_one_thousand_returns_all_decimals(): void
    {
        $helper = new CurrencyHelper(234.56789);

        $this->assertEquals('234.56789', $helper->format());
    }

    /** @test */
    public function format_greater_than_one_thousand_with_no_decimal_digits_returns_two_zeros(): void
    {
        $helper = new CurrencyHelper(2000.0);

        $this->assertEquals('2,000.00', $helper->format());
    }

    /** @test */
    public function format_one_thousand_returns_two_zeros(): void
    {
        $helper = new CurrencyHelper(1000);

        $this->assertEquals('1,000.00', $helper->format());
    }
}
