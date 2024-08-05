<?php
declare(strict_types=1);

namespace app\tests\normalizer;

use app\entity\Currency;
use app\entity\Money;
use app\normalizer\MoneyNormalizer;
use PHPUnit\Framework\TestCase;

class MoneyNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $testCases = [
            ['100.6666666', '100.67'],
            ['100.001', '100.00'],
            ['00.1', '0.10'],
            ['0.0000001', '0.00'],
            ['0.37', '0.37'],
            ['0', '0.00'],
            ['0.375', '0.38'],
            ['-0.375', '-0.38'],
            ['100000000000', '100000000000.00'],
        ];
        foreach ($testCases as [$amount, $expected]) {
            $money = new Money($amount, new Currency('EUR'));
            $normalizer = new MoneyNormalizer($money);
            $this->assertEquals($expected, $normalizer->normalize()->getAmount());
        }
    }
}