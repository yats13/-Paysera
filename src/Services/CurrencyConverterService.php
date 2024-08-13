<?php declare(strict_types=1);

namespace Paysera\Services;

use Paysera\Interfaces\CurrencyConverterInterface;
use Exception;
use Paysera\Interfaces\HttpClientInterface;

class CurrencyConverterService implements CurrencyConverterInterface
{
    private array $rates;

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
        if (!$_ENV['API_KEY']) {
            throw new Exception('API key is missing. Please set the API_KEY environment variable.');
        }

        $url = sprintf('https://api.exchangeratesapi.io/v1/latest?access_key=%s', $_ENV['API_KEY']);
        $response = $this->httpClient->get($url);
        if ($response === false) {
            throw new Exception('Error fetching exchange rates');
        }

        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (!$data['success'] || isset($data['error'])) {
            throw new Exception($data['error']["info"]);
        }

        $this->rates = $data['rates'];
    }

    public function convertToEur(float $amount, string $currency): float
    {
        $rate = $this->rates[$currency] ?? 0;
        return match ($currency) {
            'EUR' => $amount,
            default => $rate === 0 ? throw new Exception('Invalid currency or rate') : $amount / $rate,
        };
    }
}
