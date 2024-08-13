<?php

namespace Tests\Services;

use Paysera\Services\FeeCalculatorService;
use PHPUnit\Framework\TestCase;

class FeeCalculatorTest extends TestCase
{
    public function testCalculateFee(): void
    {
        $calculator = new FeeCalculatorService();

        // The expected fee for EUR transactions should match your logic.
        $this->assertEquals(1.00, $calculator->calculateFee(100, true));  // EU country
        $this->assertEquals(2.00, $calculator->calculateFee(100, false)); // Non-EU country

        // Ensure the test aligns with your calculation logic
        $this->assertEquals(0.93, $calculator->calculateFee(46.18, false));
        $this->assertEquals(0.93, $calculator->calculateFee(93, true)); // Expected 0.93
    }
}
