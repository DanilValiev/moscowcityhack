<?php

namespace App\Command;

use App\Service\Gisp\Production\ProductionDetailService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'base:productions:detail:sync', description: 'Get production detail')]
class SyncCompanyDetail extends Command
{
    public function __construct(
        private ProductionDetailService $productionDetailService
    )
    {
        parent::__construct(self::getName());
    }

    protected function configure()
    {
        $this
            ->addOption('offset', null, InputArgument::OPTIONAL, 'limit offset', 0)
            ->addOption('limit', null, InputArgument::OPTIONAL, 'limit operations', 1000000)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $offset = $input->getOption('offset');
        $limit = $input->getOption('limit');
        $progressBar = new ProgressBar($output,  $limit);
        $progressBar->start();
        $startTime = time();

        $updated = $this->productionDetailService
            ->setProgressBar($progressBar)
            ->setOffset($offset)
            ->setLimit($limit)
            ->sync()
        ;

        $progressBar->finish();
        $finishTime = time() - $startTime;
        $output->writeln("\nUpdate successfully done per {$finishTime} second! \n{$updated} productions was updated.");

        return self::SUCCESS;
    }
}