<?php declare(strict_types=1);

namespace Paysera\Interfaces;

interface CurrencyConverterInterface
{
    public function convertToEur(float $amount, string $currency): float;
}
