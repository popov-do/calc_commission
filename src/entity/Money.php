<?php
declare(strict_types=1);

namespace app\entity;

use InvalidArgumentException;

class Money
{
    private string $amount;
    private Currency $currency;

    public function __construct(
        string $amount,
        Currency $currency
    ) {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('Amount must be numeric');
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}