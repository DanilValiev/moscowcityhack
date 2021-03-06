<?php

namespace App\Command;

use App\Service\Gisp\Production\ProductionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'base:production:sync', description: 'Update the database of enterprises with gisp')]
class SyncProductions extends Command
{
    public function __construct(
        private ProductionService $productionService
    )
    {
        parent::__construct(self::getName());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $progressBar = new ProgressBar($output, 100);
        $progressBar->start();
        $progressBar->setMessage('Sync productions start...');

        $added = $this->productionService
            ->setProgressBar($progressBar)
            ->sync()
        ;

        $progressBar->finish();

        $output->writeln("Sync successfully done! \n{$added} productions was added.");

        return self::SUCCESS;
    }
}