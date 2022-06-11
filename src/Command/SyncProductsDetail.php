<?php

namespace App\Command;

use App\Service\Gisp\Product\ProductsDetailService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'base:products:detail:sync', description: 'Get details for products')]
class SyncProductsDetail extends Command
{
    public function __construct(
        private ProductsDetailService $productsDetailService
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

        $updated = $this->productsDetailService
            ->setProgressBar($progressBar)
            ->setOffset($offset)
            ->setLimit($limit)
            ->sync()
        ;

        $progressBar->finish();
        $finishTime = time() - $startTime;
        $output->writeln("\nUpdate successfully done per {$finishTime} second! \n{$updated} products was updated.");

        return self::SUCCESS;
    }
}