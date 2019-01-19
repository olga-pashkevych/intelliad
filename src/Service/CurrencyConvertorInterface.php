<?php

namespace App\Service;

interface CurrencyConvertorInterface
{
    public function convertSource(string $usdEur, string $usdChf): array;
}