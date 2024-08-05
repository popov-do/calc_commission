<?php

namespace app\tests\provider;

use app\provider\BinListApiInfoProvider;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BinListApiInfoProviderTest extends TestCase
{
    private HttpClientInterface $client;

    public function testGetCountryCode(): void
    {
        //arrange
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'country' => [
                    'alpha2' => 'DK',
                    'currency' => 'DKK',
                ],
            ]);
        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('GET', '/45717360')
            ->willReturn($response);
        $provider = new BinListApiInfoProvider($this->client, '');

        //act
        $countryCode = $provider->getIssuedCountryCode('45717360');

        //assert
        $this->assertEquals('DK', $countryCode);
    }

    public function testGetCountryCodeNotFound(): void
    {
        //arrange
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn([]);
        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('GET', '/45717361')
            ->willReturn($response);
        //assert
        $this->expectExceptionMessage('Code country not found');

        //act
        $provider = new BinListApiInfoProvider($this->client, '');
        $provider->getIssuedCountryCode('45717361');
    }

    public function testWrapException(): void
    {
        //arrange
        $this->client
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new Exception('error'));

        //assert
        $this->expectExceptionMessage('BinList service is not available');
        $this->expectExceptionCode(0);

        //act
        $provider = new BinListApiInfoProvider($this->client, '');
        $provider->getIssuedCountryCode('45717361');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createMock(HttpClientInterface::class);
    }
}