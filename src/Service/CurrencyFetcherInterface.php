<?php

namespace App\Service;

interface CurrencyFetcherInterface
{
    const USD = 'USD';
    const EUR = 'EUR';
    const CHF = 'CHF';

    public function fetchLive(string $source, array $currencies): array;
}