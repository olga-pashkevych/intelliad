<?php

namespace App\Controller;

use App\Repository\RateRepository;
use App\Repository\CurrencyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CurrencyController extends AbstractController
{
    /** @var $rateRepository RateRepository */
    protected $rateRepository;

    /** @var $currencyRepository CurrencyRepository */
    protected $currencyRepository;

    public function __construct(
        RateRepository $rateRepository,
        CurrencyRepository $currencyRepository
    )
    {
        $this->rateRepository = $rateRepository;
        $this->currencyRepository = $currencyRepository;
    }

    public function getRates(string $currency)
    {
        $currencyList = $this->currencyRepository->findCurrencies();
        if ($currency) {
            $ratesList = $this->rateRepository->findByCurrencyName($currency);
        } else {
            $ratesList = $this->rateRepository->findAllWithCurrency();
        }

        if (!$ratesList) {
            throw $this->createNotFoundException(
                'No data found'
            );
        }

        return $this->render('currency/current.html.twig', [
            'rates' => $ratesList,
            'currencies' => $currencyList
        ]);
    }
}