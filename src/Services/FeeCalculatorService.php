<?php

namespace Paysera\Services;

use Paysera\Interfaces\FeeCalculatorInterface;

class FeeCalculatorService implements FeeCalculatorInterface
{
    private const EU_COMMISSION = 0.01;
    private const NON_EU_COMMISSION = 0.02;

    public function calculateFee(float $amountInEur, bool $isEu): float
    {
        $commissionRate = $isEu ? self::EU_COMMISSION : self::NON_EU_COMMISSION;
        $fee = $amountInEur * $commissionRate;

        // Apply ceiling to the fee to ensure rounding up to the nearest cent
        return ceil($fee * 100) / 100;
    }
}
