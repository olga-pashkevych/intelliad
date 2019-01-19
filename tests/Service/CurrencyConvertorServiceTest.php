<?php

namespace App\Tests\Service;

use App\Service\CurrencyConvertorService;
use PHPUnit\Framework\TestCase;

class CurrencyConvertorServiceTest extends TestCase
{
    public function testConvertSource()
    {
        $usdEur = '0.876085';
        $usdChf = '0.987502';
        $eurUsd = 1 / $usdEur;
        $eurChf = $eurUsd * $usdChf;

        $expectedResult = array(
            'EURUSD'=>$eurUsd,
            'EURCHF'=>$eurChf
        );

        $service = new CurrencyConvertorService();
        $result = $service->convertSource($usdEur, $usdChf);

        $this->assertEquals($expectedResult, $result);
    }
}