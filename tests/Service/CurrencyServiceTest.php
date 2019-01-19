<?php

namespace App\Tests\Service;

use App\Service\CurrencyFetcherInterface;
use App\Service\CurrencyService;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use OceanApplications\currencylayer\client;

class CurrencyServiceTest extends TestCase
{
    /** @var ObjectProphecy */
    protected $currencyLayer;

    public function setUp()
    {
        $this->currencyLayer = $this->prophesize(client::class);
    }

    public function testFetchLiveSuccessfully()
    {
        $source = CurrencyFetcherInterface::USD;
        $currencies = [
            CurrencyFetcherInterface::CHF,
            CurrencyFetcherInterface::EUR
        ];

        $expectedResult = [
            'success' => true,
            'timestamp' => 1547587747,
            'quotes' => [
                'USDEUR' => 0.876085,
                'USDCHF' => 0.987502
            ]
        ];
        $this->currencyLayer->source($source)
            ->willReturn($this->currencyLayer->reveal())->shouldBeCalled();
        $this->currencyLayer->currencies(implode(',', $currencies))
            ->willReturn($this->currencyLayer->reveal())->shouldBeCalled();
        $this->currencyLayer->live()
            ->willReturn($expectedResult)->shouldBeCalled();

        $service = new CurrencyService($this->currencyLayer->reveal());
        $result = $service->fetchLive($source, $currencies);

        $this->assertEquals($expectedResult, $result);
    }

    public function testFetchLiveUnsuccessfully()
    {
        $this->expectException(\Exception::class);

        $source = CurrencyFetcherInterface::USD;
        $currencies = [
            CurrencyFetcherInterface::CHF,
            CurrencyFetcherInterface::EUR
        ];

        $expectedResult = [
            'success' => false,
             'error' => [
                 'info' => 'You have provided one or more invalid Currency Codes.'
            ]
        ];
        $this->currencyLayer->source($source)
            ->willReturn($this->currencyLayer->reveal())->shouldBeCalled();
        $this->currencyLayer->currencies(implode(',', $currencies))
            ->willReturn($this->currencyLayer->reveal())->shouldBeCalled();
        $this->currencyLayer->live()
            ->willReturn($expectedResult)->shouldBeCalled();

        $service = new CurrencyService($this->currencyLayer->reveal());
        $service->fetchLive($source, $currencies);
    }
}
