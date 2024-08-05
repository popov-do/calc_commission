<?php
declare(strict_types=1);

namespace app\tests\service;

use app\entity\CommissionCoefficient;
use app\entity\Currency;
use app\entity\Money;
use app\entity\Transaction;
use app\provider\BinInfoProviderInterface;
use app\provider\ExchangeRateProviderInterface;
use app\service\CommissionService;
use app\service\ExchangeService;
use PHPUnit\Framework\TestCase;

class CommissionServiceTest extends TestCase
{
    private ExchangeRateProviderInterface $exchangeRateProviderMock;
    private BinInfoProviderInterface $binProviderMock;

    public function testCommissionWithNonZeroRateAndNotEur(): void
    {
        //arrange
        $eur = new Currency('EUR');
        $money = new Money('2000.00', new Currency('GBP'));
        $transaction = new Transaction('4745030', $money);
        $this->binProviderMock
            ->expects($this->once())
            ->method('getIssuedCountryCode')
            ->with($transaction->getBin())
            ->willReturn('GB');
        $this->exchangeRateProviderMock
            ->expects($this->once())
            ->method('getRate')
            ->with($eur, $money->getCurrency())
            ->willReturn('0.852736');

        //act
        $commissionService = new CommissionService(
            binProvider: $this->binProviderMock,
            exchangeService: new ExchangeService($this->exchangeRateProviderMock),
            defaultCoefficient: '0.02',
            coefficients: [
                new CommissionCoefficient(['LT', 'DE'], '0.01'),
            ]
        );
        $commissionService->setTransactionCommission($transaction, $eur);

        //assert
        $this->assertEquals('46.90783', $transaction->getCommission()->getAmount());
    }

    public function testCommissionWithNonZeroRateAndEur(): void
    {
        //arrange
        $eur = new Currency('EUR');
        $money = new Money('100.00', $eur);
        $transaction = new Transaction('45717360', $money);
        $this->binProviderMock
            ->expects($this->once())
            ->method('getIssuedCountryCode')
            ->with($transaction->getBin())
            ->willReturn('LT');

        //act
        $commissionService = new CommissionService(
            binProvider: $this->binProviderMock,
            exchangeService: new ExchangeService($this->exchangeRateProviderMock),
            defaultCoefficient: '0.02',
            coefficients: [
                new CommissionCoefficient(['LT', 'DE'], '0.01'),
            ]
        );
        $commissionService->setTransactionCommission($transaction, $eur);

        //assert
        $this->assertEquals('1.00000', $transaction->getCommission()->getAmount());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->exchangeRateProviderMock = $this->createMock(ExchangeRateProviderInterface::class);
        $this->binProviderMock = $this->createMock(BinInfoProviderInterface::class);
    }
}
