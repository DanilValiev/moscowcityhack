<?php

namespace App\Command;

use App\Service\Gisp\Category\CategoryService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'base:categories:sync', description: 'Update the database of categories with gisp')]
class SyncCategories extends Command
{

    public function __construct(
        private CategoryService $categoryService
    )
    {
        parent::__construct(self::getName());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $progressBar = new ProgressBar($output, 100);
        $progressBar->start();
        $progressBar->setMessage('Sync category start...');

        $added = $this->categoryService
            ->setProgressBar($progressBar)
            ->sync()
        ;

        $progressBar->finish();

        $output->writeln("\n Sync successfully done! \n{$added} categories was added.");

        return self::SUCCESS;
    }
}