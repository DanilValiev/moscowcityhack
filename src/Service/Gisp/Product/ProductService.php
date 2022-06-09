<?php

namespace App\Service\Gisp\Product;

use App\Entity\Production;
use App\Repository\CategoriesRepository;
use App\Repository\ProductRepository;
use App\Service\CurlRequestService;
use App\Service\Gisp\Production\ProductionService;
use App\Service\Gisp\SyncInterface;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;

class ProductService implements SyncInterface
{
    private ?ProgressBar $progressBar;

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
        $categories = $this->categoriesRepository->findAll();
        $added = 0;
        $i = 0;
        $totalCount = 1100000;

        foreach ($categories as $category) {
            $products = $this->getProductsFromApi($category->getExternalId())['data'];

            foreach ($products as $product) {
                if (!$this->productRepository->checkExist($product['id'])) {
                    $production = $this->checkProduction($product['company']);
                    $productModel = $this->productFactory->build($product, $category, $production);

                    $this->productRepository->add($productModel, true);
                    $added++;
                }

                if ($this->progressBar && (++$i % intval($totalCount / 100) == 0)) {
                    $this->progressBar->advance();
                }
            }
        }

        return $added;
    }

    public function setProgressBar(ProgressBar $progressBar): self
    {
        $this->progressBar = $progressBar;

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

    private function getProductsFromApi(int $category): array
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
                \"page\": 1,
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