<?php
declare(strict_types=1);

namespace Tests\Services;

use Dotenv\Dotenv;
use Paysera\Services\CurrencyConverterService;
use Paysera\Services\HttpClientService;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2) ); // Adjust the path to your .env file
        $dotenv->load();
    }

    public function testConvertToEur(): void
    {
        // Mock the HttpClient service
        $httpClient = $this->createMock(HttpClientService::class);

        // Mock the exchange rates response
        $mockRates = [
            'success' => true,
            'rates' => [
                'USD' => 2.0,
            ],
        ];

        // Simulate a successful API response
        $httpClient->method('get')
            ->willReturn(json_encode($mockRates, JSON_THROW_ON_ERROR));

        $currencyConverter = new CurrencyConverterService($httpClient);

        // Test the conversion
        $this->assertEquals(50.0, $currencyConverter->convertToEur(100.0, 'USD'));
    }
}
