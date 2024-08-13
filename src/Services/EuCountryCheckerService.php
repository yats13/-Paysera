<?php

namespace Paysera\Services;

use Paysera\Interfaces\EuCountryCheckerInterface;

class EuCountryCheckerService implements EuCountryCheckerInterface
{
    private const EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR',
        'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'
    ];

    public function isEu(string $countryCode): bool
    {
        return in_array($countryCode, self::EU_COUNTRIES, true);
    }
}
