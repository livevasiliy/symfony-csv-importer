<?php declare(strict_types=1);

if (!function_exists('clearSymbolsFromDecimalString')) {
    function clearSymbolsFromDecimalString(string $value): ?string
    {
        $decimalWithoutAnySymbolsRegex = '/^"([\W^"])*([^\d,.]*)/';
        return preg_replace($decimalWithoutAnySymbolsRegex, '', $value);
    }
}

if (!function_exists('containSymbolsInDecimalString')) {
    function containSymbolsInDecimalString(string $value): bool
    {
        $decimalWithoutAnySymbolsRegex = '/^"([\W^"])*([^\d,.]*)/';
        return preg_match($decimalWithoutAnySymbolsRegex, $value) > 0;
    }
}

