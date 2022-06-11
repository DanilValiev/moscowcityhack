<?php

namespace App\Command;

use App\Service\Gisp\Product\ProductService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'base:products:sync', description: 'Update the database of products with gisp')]
class SyncProducts extends Command
{
    public function __construct(
        private ProductService $productService
    )
    {
        parent::__construct(self::getName());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $section1 = $output->section();
        $section2 = $output->section();

        $progressBar = new ProgressBar($section1);
        $progressBar->start();
        $progressBar->setMessage('Sync productions start...');

        $added = $this->productService
            ->setProgressBar($progressBar)
            ->setOutput($section2)
            ->sync()
        ;

        $progressBar->finish();

        $output->writeln("Sync successfully done! \n{$added} product was added.");

        return self::SUCCESS;
    }
}