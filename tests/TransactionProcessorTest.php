<?php declare(strict_types=1);

namespace Tests;

use Paysera\Interfaces\BinLookupInterface;
use Paysera\Interfaces\CurrencyConverterInterface;
use Paysera\Interfaces\FeeCalculatorInterface;
use Paysera\Interfaces\EuCountryCheckerInterface;
use Paysera\TransactionProcessor;
use PHPUnit\Framework\TestCase;

class TransactionProcessorTest extends TestCase
{
    public function testProcess(): void
    {
        // Mock dependencies
        $binLookupService = $this->createMock(BinLookupInterface::class);
        $currencyConverter = $this->createMock(CurrencyConverterInterface::class);
        $feeCalculator = $this->createMock(FeeCalculatorInterface::class);
        $euCountryChecker = $this->createMock(EuCountryCheckerInterface::class);

        $binLookupService->method('getCountryCode')->willReturn('FR');
        $currencyConverter->method('convertToEur')->willReturn(100.0);
        $feeCalculator->method('calculateFee')->willReturn(1.0);
        $euCountryChecker->method('isEu')->willReturn(true);

        $processor = new TransactionProcessor(
            $binLookupService,
            $currencyConverter,
            $feeCalculator,
            $euCountryChecker
        );

        // Simulate input file
        $inputFile = 'input.txt';
        file_put_contents($inputFile, '{"bin":"45717360","amount":"100.00","currency":"EUR"}');

        // Capture output
        ob_start();
        $processor->process($inputFile);
        $output = ob_get_clean();

        // Remove the temporary file
        unlink($inputFile);

        // Validate the output
        $this->assertStringContainsString('1.0', $output);
    }
}
