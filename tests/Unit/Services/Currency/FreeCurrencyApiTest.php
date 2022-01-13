<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Currency;

use App\Services\Currency\FreeCurrencyApi;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Safe\json_encode;

/**
 * @group unit
 */
class FreeCurrencyApiTest extends TestCase
{
    /**
     * @dataProvider extractLastRatesProvider
     *
     * @param array<string, float> $exceptedResponseData
     */
    public function test_it_should_retrieve_last_rates_for_usd_currency(
        ?string $baseApiUrl,
        ?string $apiKey,
        ?string $baseCurrency,
        ?string $requiredCurrency,
        bool $shouldThrowException,
        string $expectException,
        string $expectedExceptionMessage,
        string $exceptedMockResponse,
        ?array $exceptedResponseData,
        ?string $expectRequestUrl
    ): void
    {
        $mockResponse = new MockResponse($exceptedMockResponse, [
            'http_code' => Response::HTTP_OK,
            'response_headers' => ['Content-Type' => 'application/json']
        ]);

        $mockHttpClient = new MockHttpClient($mockResponse);
        $mockHttpClient->withOptions([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        if ($shouldThrowException) {
            static::expectException($expectException); /* @phpstan-ignore-line */
            static::expectExceptionMessage($expectedExceptionMessage);
        }

        $freeCurrencyApiService = new FreeCurrencyApi(
            $mockHttpClient,
            $baseApiUrl,    /* @phpstan-ignore-line */
            $apiKey,        /* @phpstan-ignore-line */
            $baseCurrency, /* @phpstan-ignore-line */
        );

        // Action
        $responseData = $freeCurrencyApiService->getLastRates($requiredCurrency);

        // Assert
        static::assertSame(Request::METHOD_GET, $mockResponse->getRequestMethod());
        static::assertSame(
            $expectRequestUrl,
            $mockResponse->getRequestUrl()
        );
        static::assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );

        static::assertSame($responseData, $exceptedResponseData);
    }

    /**
     * @dataProvider extractRateProvider
     *
     * @param array<string, float> $currencyRates
     */
    public function test_it_should_extract_rates_for_usd_currency(
        array $currencyRates,
        string $requiredCurrency,
        ?float $exceptedExtractedValue,
        bool $shouldThrowException,
        string $expectedExceptionMessage
    ): void
    {
        $freeCurrencyApiService = new FreeCurrencyApi(
            new MockHttpClient(),
            'foo',
            'foo'
        );

        if ($shouldThrowException) {
            static::expectException(InvalidArgumentException::class);
            static::expectErrorMessage($expectedExceptionMessage);
        }

        $result = $freeCurrencyApiService->extractCurrentRateForCurrency($currencyRates, $requiredCurrency);

        static::assertSame($result, $exceptedExtractedValue);
    }

    public function extractLastRatesProvider(): Generator
    {
        $exceptedResponseData = [
            'query' => [
                'apiKey' => '3asdjakldjl-a424-4214wdsadsad',
                'base_currency' => 'GBP',
                'timestamp' => 1642001543
            ],
            'data' => [
                'GBP' => 0.73351
            ]
        ];

        $mockResponseJson = json_encode($exceptedResponseData, JSON_THROW_ON_ERROR);

        yield 'Valid - pass all required parameters' => [
            'https://freecurrencyapi.net/api/v2/',
            'foo',
            'GBP',
            'USD',
            false,
            '',
            '',
            $mockResponseJson,
            $exceptedResponseData['data'],
            'https://freecurrencyapi.net/api/v2/latest?apikey=foo&base_currency=USD'
        ];

        yield 'Valid - pass all except requiredCurrency will be use instead of baseCurrency' => [
            'https://freecurrencyapi.net/api/v2/',
            'foo',
            'GBP',
            null,
            false,
            '',
            '',
            $mockResponseJson,
            $exceptedResponseData['data'],
            'https://freecurrencyapi.net/api/v2/latest?apikey=foo&base_currency=GBP'
        ];

        yield 'Invalid - pass all required parameters except base currency' => [
            'https://freecurrencyapi.net/api/v2/',
            'foo',
            '',
            'USD',
            true,
            InvalidArgumentException::class,
            'Not provided base currency for freecurrencyapi.net',
            $mockResponseJson,
            null,
            'https://freecurrencyapi.net/api/v2/latest?apikey=foo&base_currency=USD'
        ];

        yield 'Invalid - pass all except base url' => [
            '',
            'foo',
            'GBP',
            'USD',
            true,
            InvalidArgumentException::class,
            'Not provided base URL for freecurrencyapi.net',
            $mockResponseJson,
            null,
            'https://freecurrencyapi.net/api/v2/latest?apikey=foo&base_currency=USD'
        ];

        yield 'Invalid - pass all except apiKey' => [
            'https://freecurrencyapi.net/api/v2/',
            '',
            'GBP',
            'USD',
            true,
            InvalidArgumentException::class,
            'Not provided API key for freecurrencyapi.net',
            $mockResponseJson,
            null,
            'https://freecurrencyapi.net/api/v2/latest?apikey=foo&base_currency=USD'
        ];
    }

    public function extractRateProvider(): Generator
    {
        yield 'Valid - successfully extract existing rate' => [
            [
                'USD' => 1.363289,
                'JPY' => 156.277566,
                'CNY' => 8.671761,
            ],
            'USD',
            1.363289,
            false,
            '',
            ''
        ];

        yield 'Invalid - not available RUB rate' => [
            [
                'USD' => 1.363289,
                'JPY' => 156.277566,
                'CNY' => 8.671761,
            ],
            'RUB',
            null,
            true,
            'Cannot extract current rate for currency RUB'
        ];

        yield 'Invalid - not cannot extract rate from empty array currency rates' => [
            [],
            'RUB',
            null,
            true,
            'Cannot extract current rate for currency RUB'
        ];
    }
}
