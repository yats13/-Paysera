<?php
declare(strict_types=1);

namespace Tests\Services;

use Paysera\Services\BinLookupService;
use Paysera\Services\HttpClientService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BinLookupServiceTest extends TestCase
{
    public function testGetCountryCodeSuccess(): void
    {
        $bin = '45717360';
        $expectedCountryCode = 'DK';
        $mockResponse = json_encode(['country' => ['alpha2' => $expectedCountryCode]]);

        $httpClient = $this->createMock(HttpClientService::class);
        $httpClient->method('get')->willReturn($mockResponse);

        $binLookupService = new BinLookupService($httpClient);

        $result = $binLookupService->getCountryCode($bin);
        $this->assertEquals($expectedCountryCode, $result);
    }

    public function testGetCountryCodeRateLimitExceeded(): void
    {
        $bin = '45717360';

        $httpClient = $this->createMock(HttpClientService::class);
        $httpClient->method('get')
            ->will($this->onConsecutiveCalls(
                $this->throwException(new RuntimeException('HTTP/1.1 429 Too Many Requests')),
                $this->throwException(new RuntimeException('HTTP/1.1 429 Too Many Requests')),
                $this->throwException(new RuntimeException('HTTP/1.1 429 Too Many Requests')),
                $this->throwException(new RuntimeException('HTTP/1.1 429 Too Many Requests')),
                $this->throwException(new RuntimeException('HTTP/1.1 429 Too Many Requests'))
            ));

        $binLookupService = new BinLookupService($httpClient);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rate limit exceeded. Please try again later.');

        $binLookupService->getCountryCode($bin);
    }

    public function testGetCountryCodeInvalidBinResponse(): void
    {
        $bin = '45717360';
        $mockResponse = json_encode(['country' => ['alpha2' => null]]);

        $httpClient = $this->createMock(HttpClientService::class);
        $httpClient->method('get')->willReturn($mockResponse);

        $binLookupService = new BinLookupService($httpClient);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid BIN response');

        $binLookupService->getCountryCode($bin);
    }

    public function testGetCountryCodeCachedResult(): void
    {
        $bin = '45717360';
        $expectedCountryCode = 'DK';
        $mockResponse = json_encode(['country' => ['alpha2' => $expectedCountryCode]]);

        $httpClient = $this->createMock(HttpClientService::class);
        $httpClient->method('get')->willReturn($mockResponse);

        $binLookupService = new BinLookupService($httpClient);

        // First call should use the mocked response
        $result = $binLookupService->getCountryCode($bin);
        $this->assertEquals($expectedCountryCode, $result);

        // Second call should return cached result, so file_get_contents shouldn't be called again
        $result = $binLookupService->getCountryCode($bin);
        $this->assertEquals($expectedCountryCode, $result);
    }
}
