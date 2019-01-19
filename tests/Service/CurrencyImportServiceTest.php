<?php

namespace App\Tests\Service;

use App\Entity\Currency;
use App\Entity\Rate;
use App\Entity\Types\CustomDateTime;
use App\Repository\CurrencyRepository;
use App\Repository\RateRepository;
use App\Service\CurrencyConvertorInterface;
use App\Service\CurrencyFetcherInterface;
use App\Service\CurrencyImportService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Framework\TestCase;
use Exception;

class CurrencyImportServiceTest extends TestCase
{
    /** @var ObjectProphecy */
    protected $currencyService;

    /** @var ObjectProphecy */
    protected $currencyConvertorService;

    /** @var ObjectProphecy */
    protected $rateRepository;

    /** @var ObjectProphecy */
    protected $currencyRepository;

    public function setUp()
    {
        $this->currencyService = $this->prophesize(CurrencyFetcherInterface::class);
        $this->currencyConvertorService = $this->prophesize(CurrencyConvertorInterface::class);
        $this->rateRepository = $this->prophesize(RateRepository::class);
        $this->currencyRepository = $this->prophesize(CurrencyRepository::class);
    }

    public function testImportWithLiveData()
    {
        $expectedResult = [
            'timestamp' => 1547587747,
            'quotes' => [
                'USDEUR' => 0.876085,
                'USDCHF' => 0.987502
            ]
        ];
        $currencyData = [
            'EURUSD' => 1.876085,
            'EURCHF' => 1.996085
        ];
        $this->currencyService->fetchLive(
            CurrencyFetcherInterface::USD,
            [
                CurrencyFetcherInterface::CHF,
                CurrencyFetcherInterface::EUR
            ]
        )->willReturn($expectedResult)->shouldBeCalled();

        $this->currencyConvertorService->convertSource(
            $expectedResult['quotes']['USDEUR'],
            $expectedResult['quotes']['USDCHF']
        )->willReturn($currencyData)->shouldBeCalled();

        foreach ($currencyData as $currencyName => $rateValue) {
            $currencyEntity = new Currency();
            $currencyEntity->setName($currencyName);
            $this->currencyRepository->getByName($currencyName)
                ->willReturn($currencyEntity)
                ->shouldBeCalled();

            $rate = new Rate(
                (new CustomDateTime())->setTimestamp($expectedResult['timestamp']),
                $currencyEntity
            );
            $rate->setRate($rateValue);
            $this->rateRepository->persist($rate)->shouldBeCalled();
        }

        $this->rateRepository->flush()->shouldBeCalled();

        $importService = new CurrencyImportService(
            $this->currencyService->reveal(),
            $this->currencyConvertorService->reveal(),
            $this->rateRepository->reveal(),
            $this->currencyRepository->reveal()
        );
        $result = $importService->import();

        $this->assertEquals('New data has been added', $result);
    }

    public function testImportWithoutLiveData()
    {
        $exception = new Exception('test message');

        $this->currencyService->fetchLive(
            CurrencyFetcherInterface::USD,
            [
                CurrencyFetcherInterface::CHF,
                CurrencyFetcherInterface::EUR
            ]
        )->willThrow($exception)->shouldBeCalled();

        $importService = new CurrencyImportService(
            $this->currencyService->reveal(),
            $this->currencyConvertorService->reveal(),
            $this->rateRepository->reveal(),
            $this->currencyRepository->reveal()
        );
        $result = $importService->import();

        $this->assertEquals('test message', $result);
    }

    public function testImportException()
    {
        $exception = new Exception('test message');
        $expectedResult = [
            'timestamp' => 1547587747,
            'quotes' => [
                'USDEUR' => 0.876085,
                'USDCHF' => 0.987502
            ]
        ];
        $currencyData = [
            'EURUSD' => 1.876085,
            'EURCHF' => 1.996085
        ];
        $this->currencyService->fetchLive(
            CurrencyFetcherInterface::USD,
            [
                CurrencyFetcherInterface::CHF,
                CurrencyFetcherInterface::EUR
            ]
        )->willReturn($expectedResult)->shouldBeCalled();

        $this->currencyConvertorService->convertSource(
            $expectedResult['quotes']['USDEUR'],
            $expectedResult['quotes']['USDCHF']
        )->willReturn($currencyData)->shouldBeCalled();

        foreach ($currencyData as $currencyName => $rateValue) {
            $currencyEntity = new Currency();
            $currencyEntity->setName($currencyName);
            $this->currencyRepository->getByName($currencyName)
                ->willReturn($currencyEntity)
                ->shouldBeCalled();

            $rate = new Rate(
                (new CustomDateTime())->setTimestamp($expectedResult['timestamp']),
                $currencyEntity
            );
            $rate->setRate($rateValue);
            $this->rateRepository->persist($rate)->shouldBeCalled();
        }

        $this->rateRepository->flush()->willThrow($exception)->shouldBeCalled();

        $importService = new CurrencyImportService(
            $this->currencyService->reveal(),
            $this->currencyConvertorService->reveal(),
            $this->rateRepository->reveal(),
            $this->currencyRepository->reveal()
        );
        $result = $importService->import();

        $this->assertEquals('Something went wrong during insert into the database', $result);
    }

    public function testImportExceptionUnique()
    {
        $exception = $this->prophesize(UniqueConstraintViolationException::class);
        $expectedResult = [
            'timestamp' => 1547587747,
            'quotes' => [
                'USDEUR' => 0.876085,
                'USDCHF' => 0.987502
            ]
        ];
        $currencyData = [
            'EURUSD' => 1.876085,
            'EURCHF' => 1.996085
        ];
        $this->currencyService->fetchLive(
            CurrencyFetcherInterface::USD,
            [
                CurrencyFetcherInterface::CHF,
                CurrencyFetcherInterface::EUR
            ]
        )->willReturn($expectedResult)->shouldBeCalled();

        $this->currencyConvertorService->convertSource(
            $expectedResult['quotes']['USDEUR'],
            $expectedResult['quotes']['USDCHF']
        )->willReturn($currencyData)->shouldBeCalled();

        foreach ($currencyData as $currencyName => $rateValue) {
            $currencyEntity = new Currency();
            $currencyEntity->setName($currencyName);
            $this->currencyRepository->getByName($currencyName)
                ->willReturn($currencyEntity)
                ->shouldBeCalled();

            $rate = new Rate(
                (new CustomDateTime())->setTimestamp($expectedResult['timestamp']),
                $currencyEntity
            );
            $rate->setRate($rateValue);
            $this->rateRepository->persist($rate)->shouldBeCalled();
        }

        $this->rateRepository->flush()->willThrow($exception->reveal())->shouldBeCalled();

        $importService = new CurrencyImportService(
            $this->currencyService->reveal(),
            $this->currencyConvertorService->reveal(),
            $this->rateRepository->reveal(),
            $this->currencyRepository->reveal()
        );
        $result = $importService->import();

        $this->assertEquals('There is no new data to insert', $result);
    }
}