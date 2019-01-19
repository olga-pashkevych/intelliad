<?php

namespace App\Service;

class CurrencyConvertorService implements CurrencyConvertorInterface
{
    public function convertSource(string $usdEur, string $usdChf): array
    {
        $eurUsd = 1 / $usdEur;
        $eurChf = $eurUsd * $usdChf;

        return [
            'EURUSD' => $eurUsd,
            'EURCHF' => $eurChf
        ];
    }
}