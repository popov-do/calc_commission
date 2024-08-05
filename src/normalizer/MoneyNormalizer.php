<?php
declare(strict_types=1);

namespace app\normalizer;

use app\entity\Money;
use function DeepCopy\deep_copy;

class MoneyNormalizer
{
    public function __construct(private Money $money)
    {
    }

    public function normalize(): Money
    {
        return new Money(
            sprintf("%.2f", $this->money->getAmount()),
            deep_copy($this->money->getCurrency())
        );
    }
}