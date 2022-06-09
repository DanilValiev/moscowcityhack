<?php

namespace App\Service\Gisp;

use App\Repository\ProductionRepository;
use App\Service\CurlRequestService;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;

class ProductionService
{

    private ?ProgressBar $progressBar;

    public function __construct(
        private CurlRequestService $requestService,
        private ProductionRepository $productionRepository,
        private ProductionFactory $productionFactory,
        private string $orgApiUri,
        private string $orgDetailsUri
    )
    {
        $this->progressBar = null;
    }

    public function sync(): string
    {
        $productions = $this->getProductionsFromApi();
        $added = 0;
        $i = 1;

        foreach ($productions['items'] as $production) {
            if (!$this->productionRepository->checkExist($production['org_ogrn']))
            {
                $productionDetails = $this->getProductionDetail($production['org_ogrn']);
                $productionModel = $this->productionFactory->build($production, $productionDetails);

                $this->productionRepository->add($productionModel, true);
                $added++;
            }

            if ($this->progressBar && (++$i % intval($productions['total_count'] / 100) == 0)) {
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

    private function getProductionDetail(int $ogrn): ?array
    {
        return $this->requestService->send(
            "{$this->orgDetailsUri}/$ogrn",
            method: 'GET',
            headers: [
                'Host: gisp.gov.ru',
                'Referer: https://gisp.gov.ru/goods/'
            ]
        );
    }

    private function getProductionsFromApi(): array
    {
        $response = $this->requestService->send(
            $this->orgApiUri,
            headers: [
                'Host: gisp.gov.ru',
                'Origin: https://gisp.gov.ru',
                'Referer: https://gisp.gov.ru/pp719v2/pub/org/',
            ],
            body: '
            {
                "opt": {
                    "sort": null,
                    "requireTotalCount": true,
                    "searchOperation": "contains",
                    "searchValue": null,
                    "userData": {}
                }
            }'
        );

        if (!$response) {
            throw new RuntimeException('Gisp production API returned an empty response');
        }

        return $response;
    }
}