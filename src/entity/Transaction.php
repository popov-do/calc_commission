<?php
declare(strict_types=1);

namespace app\entity;

class Transaction
{
    private string $bin;
    private Money $money;
    private ?Money $commission;


    public function __construct(
        string $bin,
        Money $money
    ) {
        $this->bin = $bin;
        $this->money = $money;
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getCommission(): ?Money
    {
        return $this->commission;
    }

    public function setCommission(Money $commission): void
    {
        $this->commission = $commission;
    }
}