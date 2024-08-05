<?php
declare(strict_types=1);

namespace app\provider;

use app\entity\Currency;
use Exception;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRatesApiProvider implements ExchangeRateProviderInterface
{

    private ?array $rates = null;
    private HttpClientInterface $client;
    private string $apiUrl;
    private string $apiKey;

    public function __construct(
        HttpClientInterface $client,
        string $apiUrl,
        string $apiKey
    ) {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    public function getRate(Currency $from, Currency $to): string
    {
        if (isset($this->getRates()[$to->getCode()])) {
            return (string)$this->getRates()[$to->getCode()];
        }
        throw new RuntimeException('Rate not found');
    }

    private function getRates(): array
    {
        if ($this->rates === null) {
            try {
                $this->rates = $this->request('GET', '/latest')['rates'];
            } catch (Exception $e) {
                throw new RuntimeException('Unable to fetch rates', 0, $e);
            }
        }

        return $this->rates;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function request(string $method, string $url): array
    {
        $response = $this->client->request($method, $this->apiUrl . $url, [
            'headers' => [
                'apikey' => $this->apiKey,
            ],
        ]);

        return $response->toArray();
    }
}