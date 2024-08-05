<?php
declare(strict_types=1);

namespace app\provider;

interface BinInfoProviderInterface
{
    public function getIssuedCountryCode(string $bin): string;
}