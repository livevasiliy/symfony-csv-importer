<?php declare(strict_types=1);

if (!function_exists('clearSymbols')) {
    function clearSymbols(string $value): ?string
    {
        $decimalWithoutAnySymbolsRegex = '/^"([\W^"])*([^\d,.]*)/';
        return preg_replace($decimalWithoutAnySymbolsRegex, '', $value);
    }
}

if (!function_exists('containSymbols')) {
    function containSymbols(string $value): bool
    {
        $decimalWithoutAnySymbolsRegex = '/^"([\W^"])*([^\d,.]*)/';
        return preg_match($decimalWithoutAnySymbolsRegex, $value) > 0;
    }
}

