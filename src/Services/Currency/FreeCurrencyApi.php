<?php

declare(strict_types=1);

namespace App\Services\Currency;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FreeCurrencyApi implements CurrencyApi
{
    private const API_URL_LATEST_RATE = 'latest';

    private string $freeCurrencyApiBaseUrl;
    private string $freeCurrencyApiKey;

    private string $baseCurrency;

    private HttpClientInterface $httpClient;

    public function __construct(
        HttpClientInterface $httpClient,
        string $freeCurrencyApiBaseUrl,
        string $freeCurrencyApiKey,
        string $baseCurrency = 'GBP'
    ) {

        if ($freeCurrencyApiKey === '') {
            throw new InvalidArgumentException('Not provided API key for freecurrency.com');
        }

        if ($freeCurrencyApiBaseUrl === '') {
            throw new InvalidArgumentException('Not provided base URL for freecurrency.com');
        }

        $this->freeCurrencyApiBaseUrl = $freeCurrencyApiBaseUrl;
        $this->freeCurrencyApiKey = $freeCurrencyApiKey;
        $this->baseCurrency = $baseCurrency;
        $this->httpClient = $httpClient->withOptions([
            'base_uri' => $this->freeCurrencyApiBaseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * @param string|null $needleCurrency
     *
     * @return array<string, float>
     */
    public function getLastRates(?string $needleCurrency = null): array
    {
        return $this->httpClient->request(Request::METHOD_GET, self::API_URL_LATEST_RATE, [
            'query' => [
                'apikey' => $this->freeCurrencyApiKey,
                'base_currency' => $needleCurrency ?? $this->baseCurrency,
            ],
        ])->toArray()['data'];
    }

    /**
     * @param array<string, float> $currencyRates
     * @param string $needleCurrency
     *
     * @return float
     */
    public function extractCurrentRateForCurrency(array $currencyRates, string $needleCurrency): float
    {
        if (isset($currencyRates[$needleCurrency])) {
            return $currencyRates[$needleCurrency];
        }

        throw new InvalidArgumentException(sprintf('Cannot extract current rate for currency %s', $needleCurrency));
    }
}
