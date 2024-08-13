<?php
declare(strict_types=1);

namespace Paysera\Services;

use Paysera\Interfaces\HttpClientInterface;
use Paysera\Interfaces\BinLookupInterface;
use Exception;
use RuntimeException;

class BinLookupService implements BinLookupInterface
{
    private const MAX_RETRIES = 5;
    private const INITIAL_RETRY_DELAY = 2; // seconds
    private array $binCache = [];

    public function __construct(private readonly HttpClientInterface $httpClient) {}

    /**
     * @throws Exception
     */
    public function getCountryCode(string $bin): string
    {
        // Check if BIN is already cached
        if (isset($this->binCache[$bin])) {
            return $this->binCache[$bin];
        }

        $retryCount = 0;
        $retryDelay = self::INITIAL_RETRY_DELAY;

        while ($retryCount < self::MAX_RETRIES) {
            try {
                $response = $this->httpClient->get("https://lookup.binlist.net/$bin");

                if ($response === false) {
                    throw new RuntimeException('Error fetching BIN details');
                }

                $binDetails = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

                if (!isset($binDetails['country']['alpha2'])) {
                    throw new RuntimeException('Invalid BIN response');
                }

                $countryCode = $binDetails['country']['alpha2'];

                // Cache the result
                $this->binCache[$bin] = $countryCode;

                return $countryCode;

            } catch (Exception $e) {
                if (str_contains($e->getMessage(), 'HTTP/1.1 429 Too Many Requests')) {
                    $retryCount++;
                    if ($retryCount < self::MAX_RETRIES) {
                        sleep($retryDelay); // Wait before retrying
                        $retryDelay *= 2; // Exponential backoff
                    } else {
                        throw new RuntimeException('Rate limit exceeded. Please try again later.');
                    }
                } else {
                    throw $e; // Re-throw other exceptions
                }
            }
        }

        throw new RuntimeException('Failed to retrieve BIN details after multiple attempts.');
    }
}
