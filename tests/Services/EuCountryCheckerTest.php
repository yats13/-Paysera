<?php declare(strict_types=1);


namespace Tests\Services;

use Paysera\Services\EuCountryCheckerService;
use PHPUnit\Framework\TestCase;

class EuCountryCheckerTest extends TestCase
{
    public function testIsEu(): void
    {
        $checker = new EuCountryCheckerService();

        $this->assertTrue($checker->isEu('FR'));
        $this->assertFalse($checker->isEu('US'));
    }
}
