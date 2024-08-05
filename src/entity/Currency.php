<?php
declare(strict_types=1);

namespace app\entity;

use InvalidArgumentException;

class Currency
{
    private string $currencyCode;

    public function __construct(
        string $currencyCode
    ) {
        if (strlen($currencyCode) !== 3) {
            throw new InvalidArgumentException('Currency code must be 3 characters');
        }
        $this->currencyCode = $currencyCode;
    }

    public function areEquals(Currency $to): bool
    {
        return $this->getCode() === $to->getCode();
    }

    public function getCode(): string
    {
        return $this->currencyCode;
    }
}