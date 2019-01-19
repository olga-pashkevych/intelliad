<?php

namespace App\Tests\Command;

use App\Command\CurrencyCommand;
use App\Repository\RateRepository;
use App\Service\CurrencyImportInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CurrencyCommandTest extends TestCase
{
    /** @var ObjectProphecy */
    protected $rateRepository;

    /** @var ObjectProphecy */
    protected $currencyImportService;

    public function setUp()
    {
        $this->rateRepository = $this->prophesize(RateRepository::class);
        $this->currencyImportService = $this->prophesize(CurrencyImportInterface::class);
    }

    public function testExecuteWithoutShowDataOption()
    {
        $this->rateRepository->findAllWithCurrency()->shouldNotBeCalled();
        $this->currencyImportService->import()->willReturn('message')->shouldBeCalled();

        $currencyCommand = new CurrencyCommand(
            $this->rateRepository->reveal(),
            $this->currencyImportService->reveal()
        );
        $tester = $this->getCommandTester($currencyCommand);
        $tester->execute([]);
        $this->assertEquals('[OK] message', trim($tester->getDisplay()));
    }

    public function testExecuteWithShowDataOption()
    {
        $this->rateRepository->findAllWithCurrency()->willReturn([
            ['EURUSD', 1.139751, '17-01-2019 10:09'],
            ['EURUSD', 1.139751, '17-01-2019 10:09'],
        ])->shouldBeCalled();
        $this->currencyImportService->import()->willReturn('message')->shouldBeCalled();

        $currencyCommand = new CurrencyCommand(
            $this->rateRepository->reveal(),
            $this->currencyImportService->reveal()
        );
        $tester = $this->getCommandTester($currencyCommand);
        $tester->execute(['--show-data' => true]);
        $expectedOutput = <<<Output
+----------+----------+------------------+
| Currency | Rate     | Date             |
+----------+----------+------------------+
| EURUSD   | 1.139751 | 17-01-2019 10:09 |
| EURUSD   | 1.139751 | 17-01-2019 10:09 |
+----------+----------+------------------+

 [OK] message
Output;

        $this->assertEquals($expectedOutput, trim($tester->getDisplay()));
    }

    protected function getCommandTester(CurrencyCommand $currencyCommand): CommandTester
    {
        $application = new Application();
        $application->add($currencyCommand);
        $command = $application->find('currency:import');
        return new CommandTester($command);
    }
}