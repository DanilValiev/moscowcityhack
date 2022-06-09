<?php

namespace App\Service\Gisp\Category;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Service\CurlRequestService;
use App\Service\Gisp\SyncInterface;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;

class CategoryService implements SyncInterface
{

    private ?ProgressBar $progressBar;

    public function __construct(
        private CategoriesRepository $categoriesRepository,
        private CurlRequestService $requestService,
        private string $productReferencesUrl
    )
    {
        $this->progressBar = null;
    }

    public function sync(): int
    {
        $categories = $this->getCategoriesFromApi()['industries'];
        $totalCount = count($categories);
        $i = 0;
        $added = 0;

        foreach ($categories as $category) {
            if (!$this->categoriesRepository->checkExist($category['id'])) {
                $model = new Categories();

                $model
                    ->setExternalId($category['id'])
                    ->setCode($category['code'])
                    ->setName($category['name'])
                ;

                $this->categoriesRepository->add($model, true);
                $added++;
            }

            if ($this->progressBar && (++$i % intval($totalCount / 100) == 0)) {
                $this->progressBar->advance();
            }
        }

        return $added;
    }

    public function setProgressBar(ProgressBar $progressBar): self
    {
        $this->progressBar = $progressBar;

        return $this;
    }

    private function getCategoriesFromApi(): array
    {
        $response = $this->requestService->send(
            $this->productReferencesUrl,
            method: 'GET',
            headers: [
                'Host: gisp.gov.ru',
                'Referer: https://gisp.gov.ru/goods/'
            ]
        );

        if (!$response) {
            throw new RuntimeException('Gisp category API returned an empty response');
        }

        return $response;
    }
}