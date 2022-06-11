<?php

namespace App\Service\Gisp\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CurlRequestService;
use App\Service\Gisp\SyncInterface;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;

class ProductsDetailService implements SyncInterface
{
    private ?ProgressBar $progressBar;

    private int $offset;

    private int $limit;

    public function __construct(
        private CurlRequestService $requestService,
        private ProductRepository $productRepository,
        private ProductFactory $productFactory,
        private string $apiDetailProduct
    )
    {
        $this->progressBar = null;
        $this->offset = 0;
        $this->limit = 1000000;
    }

    public function sync(): int
    {
        $updated = 0;
        $products = $this->productRepository->findAllIterable($this->offset, $this->limit);

        /** @var Product $product */
        foreach ($this->progressBar->iterate($products) as $product)
        {
            $productDetail = $this->getDetailProductsApi($product->getExternalId());

            $this->productFactory->buildDetail($product, $productDetail);

            $this->productRepository->flush();
            $this->productRepository->detach($product);
            $updated++;
        }

        return $updated;
    }

    public function setProgressBar(ProgressBar $progressBar): self
    {
        $this->progressBar = $progressBar;

        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    private function getDetailProductsApi(string $productId): array
    {
        $response = $this->requestService->send(
            "{$this->apiDetailProduct}/{$productId}",
            'GET',
            headers: [
                'Host: gisp.gov.ru',
                'Referer: https://gisp.gov.ru/goods/',
            ]
        );

        if (!$response) {
            throw new RuntimeException("Gisp reestr API returned an empty response, ID: {$productId}");
        }

        return $response;
    }
}