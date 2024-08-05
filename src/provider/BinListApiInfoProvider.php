<?php
declare(strict_types=1);

namespace app\provider;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinListApiInfoProvider implements BinInfoProviderInterface
{
    private HttpClientInterface $client;
    private string $apiUrl;

    public function __construct(
        HttpClientInterface $client,
        string $apiUrl
    ) {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
    }

    public function getIssuedCountryCode(string $bin): string
    {
        if (strlen($bin) < 6 || !is_numeric($bin)) {
            throw new InvalidArgumentException('Invalid bin');
        }

        $response = $this->request('GET', $bin);
        if (!isset($response['country']['alpha2'])) {
            throw new RuntimeException('Code country not found');
        }

        if (!isset($response['country']['currency'])) {
            throw new RuntimeException('Currency not found');
        }

        if (!is_string($response['country']['alpha2']) || strlen($response['country']['alpha2']) !== 2) {
            throw new RuntimeException('Invalid alpha2');
        }

        return $response['country']['alpha2'];
    }

    private function request(string $method, string $url): array
    {
        try {
            $response = $this->client->request($method, $this->apiUrl . '/' . $url);

            return $response->toArray();
        } catch (Exception $e) {
            throw new RuntimeException('BinList service is not available', 0, $e);
        }
    }
}