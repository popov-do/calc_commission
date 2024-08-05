<?php
declare(strict_types=1);

namespace app\service;

use app\entity\CommissionCoefficient;
use app\entity\Currency;
use app\entity\Money;
use app\entity\Transaction;
use app\provider\BinInfoProviderInterface;

class CommissionService
{
    private int $accuracy;
    /**
     * @var CommissionCoefficient[]
     */
    private array $coefficients;
    private string $defaultCoefficient;
    private BinInfoProviderInterface $binProvider;
    private ExchangeService $exchangeService;

    public function __construct(
        BinInfoProviderInterface $binProvider,
        ExchangeService $exchangeService,
        string $defaultCoefficient,
        array $coefficients
    ) {
        $this->defaultCoefficient = $defaultCoefficient;
        $this->coefficients = $coefficients;
        $this->binProvider = $binProvider;
        $this->exchangeService = $exchangeService;
        $this->accuracy = 5;
    }

    public function setTransactionCommission(Transaction $transaction, Currency $currency): void
    {
        $countryCode = $this->binProvider->getIssuedCountryCode($transaction->getBin());
        $exchangedMoney = $this->exchangeService->exchange($transaction->getMoney(), $currency);
        $transaction->setCommission(
            new Money(
                bcmul($exchangedMoney->getAmount(), $this->getCommissionCoefficient($countryCode), $this->accuracy),
                $currency
            )
        );
    }

    private function getCommissionCoefficient(string $countryCode): string
    {
        foreach ($this->coefficients as $coefficient) {
            if (in_array($countryCode, $coefficient->getCountryCodes(), true)) {
                return $coefficient->getCommissionCoefficient();
            }
        }

        return $this->defaultCoefficient;
    }
}