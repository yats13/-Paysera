<?php

use Paysera\Services\BinLookupService;
use Paysera\Services\CurrencyConverterService;
use Paysera\Services\FeeCalculatorService;
use Paysera\Services\EuCountryCheckerService;
use Paysera\Services\HttpClientService;
use Paysera\TransactionProcessor;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$httpClientService = new HttpClientService();
$processor = new TransactionProcessor(new BinLookupService($httpClientService), new CurrencyConverterService($httpClientService), new FeeCalculatorService(), new EuCountryCheckerService());
$processor->process($argv[1]);
