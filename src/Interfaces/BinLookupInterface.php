<?php declare(strict_types=1);

namespace Paysera\Interfaces;

interface BinLookupInterface
{
    public function getCountryCode(string $bin): string;
}
