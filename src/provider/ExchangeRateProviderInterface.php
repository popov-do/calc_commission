<?php
declare(strict_types=1);

namespace app\provider;

use app\entity\Currency;

interface ExchangeRateProviderInterface
{
    public function getRate(Currency $from, Currency $to): string;
}