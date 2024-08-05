<?php
declare(strict_types=1);

require 'vendor/autoload.php';

$euCoefficient = new \app\entity\CommissionCoefficient(
    countryCodes: [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK',
    ],
    commissionCoefficient: '0.01'
);
$repository = new \app\repository\FileTransactionRepository($argv[1]);
$binProvider = new \app\provider\BinListApiInfoProvider(
    client: new \Symfony\Component\HttpClient\CurlHttpClient(),
    apiUrl: "https://lookup.binlist.net"
);

$commissionService = new \app\service\CommissionService(
    binProvider: $binProvider,
    exchangeService: new \app\service\ExchangeService(
        new \app\provider\ExchangeRatesApiProvider(
            client: new \Symfony\Component\HttpClient\CurlHttpClient(),
            apiUrl: "https://api.apilayer.com/exchangerates_data",
            apiKey: 'YOUR_API_KEY'
        ),
    ),
    defaultCoefficient: '0.02',
    coefficients: [
        $euCoefficient,
    ]
);

foreach ($repository->find() as $transaction) {
    try {
        $commissionService->setTransactionCommission($transaction, new \app\entity\Currency('EUR'));
        $moneyNormalizer = new \app\normalizer\MoneyNormalizer(
            $transaction->getCommission()
        );
        echo $moneyNormalizer->normalize()->getAmount() . PHP_EOL;
    } catch (Exception $e) {
        echo sprintf('Error: %s %s', $e->getMessage(), $e->getPrevious()?->getMessage()) . PHP_EOL;
        continue;
    }
}