<?php

declare(strict_types=1);

namespace App\Services\Currency;

interface CurrencyApi
{
    /**
     * @param string|null $needleCurrency
     *
     * @return array<string, float>
     */
    public function getLastRates(?string $needleCurrency = null): array;

    /**
     * @param array<string, float> $currencyRates
     * @param string $needleCurrency
     *
     * @return float
     */
    public function extractCurrentRateForCurrency(array $currencyRates, string $needleCurrency): float;
}
