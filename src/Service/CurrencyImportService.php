<?php

namespace App\Service;

use App\Entity\Rate;
use App\Entity\Types\CustomDateTime;
use App\Repository\CurrencyRepository;
use App\Repository\RateRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class CurrencyImportService implements CurrencyImportInterface
{
    /** @var $currencyService CurrencyFetcherInterface */
    protected $currencyService;

    /** @var $currencyConvertorService CurrencyConvertorInterface */
    protected $currencyConvertorService;

    /** @var $rateRepository RateRepository */
    protected $rateRepository;

    /** @var $currencyRepository CurrencyRepository */
    protected $currencyRepository;

    public function __construct(
        CurrencyFetcherInterface $currencyService,
        CurrencyConvertorInterface $currencyConvertorService,
        RateRepository $rateRepository,
        CurrencyRepository $currencyRepository
    )
    {
        $this->currencyService = $currencyService;
        $this->currencyConvertorService = $currencyConvertorService;
        $this->rateRepository = $rateRepository;
        $this->currencyRepository = $currencyRepository;
    }

    public function import(): string
    {
        try {
            $result = $this->currencyService->fetchLive(
                CurrencyFetcherInterface::USD,
                [
                    CurrencyFetcherInterface::CHF,
                    CurrencyFetcherInterface::EUR
                ]
            );
        } catch (\Exception $e){
            return $e->getMessage();
        }

        $currencyData = $this->currencyConvertorService->convertSource(
            $result['quotes']['USDEUR'],
            $result['quotes']['USDCHF']
        );

        foreach ($currencyData as $currencyName => $rateValue) {
            $currency = $this->currencyRepository->getByName($currencyName);

            $rate = new Rate(
                (new CustomDateTime())->setTimestamp($result['timestamp']),
                $currency
            );
            $rate->setRate($rateValue);
            $this->rateRepository->persist($rate);
        }

        try {
            $this->rateRepository->flush();
            $message = 'New data has been added';
        } catch (UniqueConstraintViolationException $e) {
            $message =  'There is no new data to insert';
        } catch (\Exception $e) {
            $message =  'Something went wrong during insert into the database';
        }

        return $message;
    }
}