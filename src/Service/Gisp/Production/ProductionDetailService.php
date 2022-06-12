<?php

namespace App\Service\Gisp\Production;

use App\Entity\Production;
use App\Repository\ProductionRepository;
use App\Service\CurlRequestService;
use App\Service\Gisp\SyncInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ProductionDetailService implements SyncInterface
{
    private ?ProgressBar $progressBar;

    private int $offset;

    private int $limit;

    public function __construct(
        private CurlRequestService $requestService,
        public ProductionRepository $productionRepository,
        private ProductionFactory $productionFactory,
        private string $fnsApiUrl
    )
    {
        $this->progressBar = null;
        $this->offset = 0;
        $this->limit = 1000000;
    }

    public function sync(): int
    {
        $updated = 0;
        $productions = $this->productionRepository->findAllIterable($this->offset, $this->limit);

        /** @var Production $production */
        foreach ($this->progressBar->iterate($productions) as $production) {
            $detailInfo = $this->getDetailProductsApi($this->getCompanyIndentity($production));

            if (!$detailInfo) {
                continue ;
            }

            $this->productionFactory->buildDetailData($production, $detailInfo);

            $updated++;
            $this->productionRepository->flush();
            $this->productionRepository->detach($production);
        }

        return $updated;
    }

    public function setProgressBar(ProgressBar $progressBar): SyncInterface
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

    private function getCompanyIndentity(Production $production): ?string
    {
        $response = null;
        $inn = $production->getInn();
        $ogrn = $production->getOgrn();

        if ($inn && (strlen($inn) == 10 || strlen($inn) == 12)) {
            $response =  $inn;
        }
        else if ($ogrn && (strlen($ogrn) == 13 || strlen($ogrn) == 15)) {
            $response =  $ogrn;
        }

        return $response;
    }

    private function getDetailProductsApi(?string $productionId): ?array
    {
        if (!$productionId) {
            return null;
        }

        $response = $this->requestService->send(
            "{$this->fnsApiUrl}/{$productionId}.json",
            'GET'
        );

        if (!$response) {
            print_r("Reestr API returned an empty response, ID: {$productionId} \n");
        }

        if (isset($response['error'])) {
            return null;
        }

        return $response;
    }
}