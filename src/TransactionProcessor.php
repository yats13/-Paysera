<?php

namespace Paysera;

use Paysera\Interfaces\BinLookupInterface;
use Paysera\Interfaces\CurrencyConverterInterface;
use Paysera\Interfaces\FeeCalculatorInterface;
use Paysera\Interfaces\EuCountryCheckerInterface;
use Exception;

final class TransactionProcessor
{
    private array $binCache = [];

    public function __construct(
        private readonly BinLookupInterface $binLookupService,
        private readonly CurrencyConverterInterface $currencyConverter,
        private readonly FeeCalculatorInterface $feeCalculator,
        private readonly EuCountryCheckerInterface $euCountryChecker
    ) {}

    public function process(string $filePath): void
    {
        $rows = explode("\n", file_get_contents($filePath));
        foreach ($rows as $row) {
            if (empty($row)) {
                continue;
            }

            $data = json_decode($row, true, 512, JSON_THROW_ON_ERROR);
            $bin = $data['bin'];
            $amount = (float) $data['amount'];
            $currency = $data['currency'];

            try {
                // Check if the BIN has already been looked up
                if (isset($this->binCache[$bin])) {
                    $countryCode = $this->binCache[$bin];
                } else {
                    $countryCode = $this->binLookupService->getCountryCode($bin);
                    // Store the result in the cache
                    $this->binCache[$bin] = $countryCode;
                }

                $isEu = $this->euCountryChecker->isEu($countryCode);
                $amountInEur = $this->currencyConverter->convertToEur($amount, $currency);
                $fee = $this->feeCalculator->calculateFee($amountInEur, $isEu);

                // Ensure the fee is formatted to one decimal place
                echo number_format($fee, 1, '.', '') . "\n";
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage() . "\n";
            }
        }
    }
}
