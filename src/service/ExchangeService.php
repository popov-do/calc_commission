<?php
declare(strict_types=1);

namespace app\service;

use app\entity\Currency;
use app\entity\Money;
use app\provider\ExchangeRateProviderInterface;
use function DeepCopy\deep_copy;

class ExchangeService
{
    private int $accuracy;
    private ExchangeRateProviderInterface $exchangeRateProvider;

    public function __construct(
        ExchangeRateProviderInterface $exchangeRateProvider,
    ) {
        $this->exchangeRateProvider = $exchangeRateProvider;
        $this->accuracy = 5;
    }

    public function exchange(Money $money, Currency $exchangeCurrency): Money
    {
        $exchangeCurrency = deep_copy($exchangeCurrency);
        if ($money->getCurrency()->areEquals($exchangeCurrency)) {
            return new Money($money->getAmount(), $exchangeCurrency);
        }

        $rate = $this->exchangeRateProvider->getRate($exchangeCurrency, $money->getCurrency());
        if (bccomp($rate, '0', $this->accuracy)) {
            $toAmount = bcdiv(
                $money->getAmount(),
                $rate,
                $this->accuracy
            );
        } else {
            $toAmount = $money->getAmount();
        }

        return new Money($toAmount, $exchangeCurrency);
    }
}