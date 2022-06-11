<?php

namespace App\Service\Gisp\Product;

use App\Entity\Categories;
use App\Entity\Production;
use App\Repository\CategoriesRepository;
use App\Repository\ProductRepository;
use App\Service\CurlRequestService;
use App\Service\Gisp\Production\ProductionService;
use App\Service\Gisp\SyncInterface;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ProductService implements SyncInterface
{
    private ?ProgressBar $progressBar;

    private ProgressBar $secondProgressBar;

    private OutputInterface  $output;

    public function __construct(
        private ProductRepository $productRepository,
        private CategoriesRepository $categoriesRepository,
        private CurlRequestService $requestService,
        private ProductFactory $productFactory,
        private ProductionService $productionService,
        private string $productApiUri
    )
    {
        $this->progressBar = null;
    }

    public function sync(): int
    {
        $categories = $this->categoriesRepository->findAllByLimit(80, 10);
        print_r($categories[0]->getExternalId());
        die;
        $added = 0;

        foreach ($this->progressBar->iterate($categories) as $category) {
            $currentItem = 0;
            $this->progressBar->setMessage("{$category->getId()} + 1");
            $products = $this->getProductsFromApi($category->getExternalId());
            $this->secondProgressBar = new ProgressBar($this->output, 100);
            $this->secondProgressBar->setFormat('custom2');
            $this->secondProgressBar->start();

//            if ($products['meta']['total'] > 5000)
//            {
//                $maxPage = ceil($products['meta']['total'] / 5000);
//                $currenPage = 1;
//
//                while ($currenPage <= $maxPage)
//                {
//                    if ($currenPage != 1) {
//                        $products = $this->getProductsFromApi($category->getExternalId(), $currenPage);
//                        print_r("{$currenPage}\n");
//                    }
//
//                    $this->iterateProduction($products['data'], $added, $category, $currentItem, $products['meta']['total']);
//                    $currenPage++;
//                }
//            } else
//            {
                $this->iterateProduction($products['data'], $added, $category, $currentItem, $products['meta']['total']);
//            }

            $this->secondProgressBar->finish();
            $this->output->clear();
        }

        return $added;
    }

    private function iterateProduction(array $products, int &$added, Categories $category, int &$currentItem, int $total)
    {
        foreach ($products as $product) {
            if (!$this->productRepository->checkExist($product['id'])) {
                $production = $this->checkProduction($product['company']);
                $productModel = $this->productFactory->build($product, $category, $production);

                $this->productRepository->add($productModel, true);
                $added++;

            }

            if (++$currentItem % intval($total / 100) == 0) {
                $this->secondProgressBar->advance();
                $this->secondProgressBar->setMessage($currentItem, 'currentItem');
                $this->secondProgressBar->setMessage($total, 'totalItem');
            }
        }
    }

    public function setProgressBar(ProgressBar $progressBar): self
    {
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] -- Current category id: %message%');
        ProgressBar::setFormatDefinition('custom2', ' %current%/%max% [%bar%] %percent% -- %currentItem%/%totalItem%');
        $this->progressBar = $progressBar;
        $this->progressBar->setFormat('custom');

        return $this;
    }

    public function setOutput(OutputInterface $output): self
    {
        $this->output = $output;

        return $this;
    }

    private function checkProduction(array $companyDetails): Production
    {
        $company = $this->productionService->productionRepository->findOneBy(['Ogrn' => $companyDetails['ogrn']]);

        if ($company != null) {
            return $company;
        } else {
           return $this->productionService->addProductionFromProduct($companyDetails);
        }
    }

    private function getProductsFromApi(int $category, int $page = 1): array
    {
        $response = $this->requestService->send(
            $this->productApiUri,
            headers: [
                'Host: gisp.gov.ru',
                'Origin: https://gisp.gov.ru',
                'Referer: https://gisp.gov.ru/goods/',
            ],
            body: "
           {
                \"page\": {$page},
                \"per_page\": 10000,
                \"filters\": {
                    \"status_code\": \"product\",
                    \"industry_ids\": [{$category}]
                }
            }"
        );

        if (!$response) {
            throw new RuntimeException('Gisp product API returned an empty response');
        }

        return $response;
    }
}