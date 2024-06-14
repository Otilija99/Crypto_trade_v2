<?php

namespace App\Client;

use GuzzleHttp\Client;
use App\Model\Cryptocurrency;
use Dotenv\Dotenv;

class CoinMarketCapApiClient implements ApiClientInterface
{
    private string $apiKey;
    private Client $client;

    public function __construct()
    {
        // Load environment variables from .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Adjust path as needed
        $dotenv->load();

        // Get API key from environment variables
        $this->apiKey = $_ENV['COINMARKETCAP_API_KEY'];

        // Initialize Guzzle client
        $this->client = new Client([
            'base_uri' => 'https://pro-api.coinmarketcap.com/v1/',
            'headers' => [
                'X-CMC_PRO_API_KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function getTopCryptocurrencies(): array
    {
        $response = $this->client->get('cryptocurrency/listings/latest');
        $data = json_decode($response->getBody()->getContents(), true);
        $cryptocurrencies = [];

        foreach ($data['data'] as $currency) {
            $cryptocurrencies[] = new Cryptocurrency(
                $currency['symbol'],
                $currency['name'],
                $currency['quote']['USD']['price']
            );
        }

        return $cryptocurrencies;
    }

    public function getCurrentPrice(string $symbol): float
    {
        $response = $this->client->get('cryptocurrency/quotes/latest', [
            'query' => ['symbol' => $symbol]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data'][$symbol]['quote']['USD']['price'];
    }
}
