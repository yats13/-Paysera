<?php

namespace Paysera\Interfaces;

interface FeeCalculatorInterface
{
    public function calculateFee(float $amountInEur, bool $isEu): float;
}
