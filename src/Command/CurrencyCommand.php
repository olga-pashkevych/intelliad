<?php

namespace App\Command;

use App\Service\CurrencyImportInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\RateRepository;

class CurrencyCommand extends Command
{
    protected static $defaultName = 'currency:import';

    /** @var $rateRepository RateRepository */
    protected $rateRepository;

    /** @var $currencyImportService CurrencyImportInterface */
    protected $currencyImportService;


    public function __construct(
        RateRepository $rateRepository,
        CurrencyImportInterface $currencyImportService
    )
    {
        parent::__construct();
        $this->rateRepository = $rateRepository;
        $this->currencyImportService = $currencyImportService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Currency importer')
            ->addOption('show-data', null, InputOption::VALUE_NONE, 'Show imported data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $message = $this->currencyImportService->import();

        if ($input->getOption('show-data')) {
            $ratesList = $this->rateRepository->findAllWithCurrency();

            $table = new Table($output);
            $table->setHeaders(['Currency', 'Rate', 'Date'])
                  ->setRows($ratesList);
            $table->render();
        }

        $io->success($message);
    }

}
