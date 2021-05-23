<?php

namespace Tests\Unit;

use App\Helpers\TrendHelper;
use PHPUnit\Framework\TestCase;

class TrendHelperTest extends TestCase
{
    /** @test */
    public function format_true_returns_correct_string(): void
    {
        $helper = new TrendHelper(true);

        $this->assertEquals('✅', $helper->format());
    }

    /** @test */
    public function format_false_returns_correct_string(): void
    {
        $helper = new TrendHelper(false);

        $this->assertEquals('🛑', $helper->format());
    }
}
