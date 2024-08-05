<?php
declare(strict_types=1);

namespace app\tests\service;

use app\entity\Currency;
use app\entity\Money;
use app\provider\ExchangeRateProviderInterface;
use app\service\ExchangeService;
use PHPUnit\Framework\TestCase;

class ExchangeServiceTest extends TestCase
{
    private ExchangeRateProviderInterface $exchangeRateProviderMock;
    private ExchangeService $exchangeService;

    public function testExchangeWithZeroRate(): void
    {
        //arrange
        $eur = new Currency('EUR');
        $money = new Money('2000.00', new Currency('GBP'));
        $this->exchangeRateProviderMock
            ->expects($this->once())
            ->method('getRate')
            ->with($eur, $money->getCurrency())
            ->willReturn('0');

        //act
        $result = $this->exchangeService->exchange($money, $eur);

        //assert
        $this->assertEquals('2000.00', $result->getAmount());
        $this->assertEquals('EUR', $result->getCurrency()->getCode());
    }

    public function testExchangeWithNonZeroRateAndNotEur(): void
    {
        //arrange
        $eur = new Currency('EUR');
        $money = new Money('2000.00', new Currency('GBP'));
        $this->exchangeRateProviderMock
            ->expects($this->once())
            ->method('getRate')
            ->with($eur, $money->getCurrency())
            ->willReturn('0.852736');

        //act
        $result = $this->exchangeService->exchange($money, $eur);

        //assert
        $this->assertEquals('2345.39177', $result->getAmount());
        $this->assertEquals('EUR', $result->getCurrency()->getCode());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->exchangeRateProviderMock = $this->createMock(ExchangeRateProviderInterface::class);
        $this->exchangeService = new ExchangeService($this->exchangeRateProviderMock);
    }
}
