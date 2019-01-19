<?php

namespace App\Service;

use OceanApplications\currencylayer\client;

class CurrencyService implements CurrencyFetcherInterface
{
    /** @var $currencyLayer client */
    protected $currencyLayer;

    public function __construct(client $currencylayer)
    {
        $this->currencyLayer = $currencylayer;
    }

    public function fetchLive(string $source, array $currencies): array
    {
        $result = $this->currencyLayer
            ->source($source)
            ->currencies(implode(',', $currencies))
            ->live();

        if (!$result['success']) {
            throw new \Exception($result['error']['info']);
        }

        return $result;
    }
}