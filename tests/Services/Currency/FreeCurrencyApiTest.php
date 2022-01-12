<?php declare(strict_types=1);

namespace App\Tests\Services\Currency;

use App\Services\Currency\FreeCurrencyApi;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Safe\json_encode;

class FreeCurrencyApiTest extends TestCase
{
    public function test_it_should_retrieve_last_rates_for_usd_currency(): void
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
        $mockResponse = new MockResponse($mockResponseJson, [
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

        $freeCurrencyApiService = new FreeCurrencyApi(
            $mockHttpClient,
            'https://freecurrencyapi.net/api/v2/',
            'foo',
            'GBP'
        );

        // Action
        $responseData = $freeCurrencyApiService->getLastRates('USD');

        // Assert
        static::assertSame(Request::METHOD_GET, $mockResponse->getRequestMethod());
        static::assertSame(
            'https://freecurrencyapi.net/api/v2/latest?apikey=foo&base_currency=USD',
            $mockResponse->getRequestUrl()
        );
        static::assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );
        static::assertSame($responseData, $exceptedResponseData['data']);
    }

    /**
     * @dataProvider extractRateProvider
     *
     * @param array<string, float> $currencyRates
     * @param string $requiredCurrency
     * @param float|null $exceptedExtractedValue
     * @param bool $shouldThrowException
     * @param string $expectedExceptionMessage
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

    // TODO: Coverage test case when throw exception from constructor
}
