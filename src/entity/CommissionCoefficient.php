<?php

namespace app\entity;

use InvalidArgumentException;

class CommissionCoefficient
{
    private array $countryCodes;
    private string $commissionCoefficient;

    public function __construct(
        array $countryCodes,
        string $commissionCoefficient
    ) {
        if (is_numeric($commissionCoefficient) === false) {
            throw new InvalidArgumentException('Commission coefficient must be a number');
        }

        $this->countryCodes = $countryCodes;
        $this->commissionCoefficient = $commissionCoefficient;
    }

    public function getCountryCodes(): array
    {
        return $this->countryCodes;
    }

    public function getCommissionCoefficient(): string
    {
        return $this->commissionCoefficient;
    }
}