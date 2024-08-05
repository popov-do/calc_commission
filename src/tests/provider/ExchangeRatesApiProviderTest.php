<?php

namespace app\tests\provider;

use app\entity\Currency;
use app\provider\ExchangeRatesApiProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ExchangeRatesApiProviderTest extends TestCase
{
    public function testValidRateIsReturned(): void
    {
        $client = $this->createMock(HttpClientInterface::class);
        $client->method('request')->willReturn($this->createMockResponse(['rates' => ['USD' => '1.2']]));

        $provider = new ExchangeRatesApiProvider($client, 'https://api.example.com', 'api_key');
        $rate = $provider->getRate(new Currency('EUR'), new Currency('USD'));

        $this->assertSame('1.2', $rate);
    }

    private function createMockResponse(array $data): object
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn($data);

        return $response;
    }

    public function testRateNotFoundThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rate not found');

        $client = $this->createMock(HttpClientInterface::class);
        $client->method('request')->willReturn($this->createMockResponse(['rates' => ['GBP' => '1.2']]));

        $provider = new ExchangeRatesApiProvider($client, 'https://api.example.com', 'api_key');
        $provider->getRate(new Currency('GBP'), new Currency('USD'));
    }

    public function testUnableToFetchRatesThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to fetch rates');

        $client = $this->createMock(HttpClientInterface::class);
        $client->method('request')->willThrowException(new TransportException());

        $provider = new ExchangeRatesApiProvider($client, 'https://api.example.com', 'api_key');
        $provider->getRate(new Currency('EUR'), new Currency('USD'));
    }
}