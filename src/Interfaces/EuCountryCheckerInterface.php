<?php declare(strict_types=1);

namespace Paysera\Interfaces;

interface EuCountryCheckerInterface
{
    public function isEu(string $countryCode): bool;
}
