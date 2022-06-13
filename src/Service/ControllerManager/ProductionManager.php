<?php

namespace App\Service\ControllerManager;

use App\Entity\ContactInfo;
use App\Entity\Odkv;
use App\Entity\Production;
use App\Repository\ProductionRepository;
use Doctrine\Common\Collections\Collection;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ProductionManager
{
    public function __construct(
        private ProductionRepository $productionRepository
    )
    {
    }

    public function getById(string $id) {
        $production = $this->productionRepository->findOneBy(['id' => $id]);
        $response = null;

        if ($production) {
            $response = $this->prepareResponseArray($production);
        }

        return $response;
    }

    public function getByName(string $name): array
    {
        $productions = $this->productionRepository->findProductionByName($name);
        $response = ['data' => []];

        if ($productions) {
            foreach ($productions as $production) {
                $response['data'][] = $this->prepareResponseArray($production);
            }
        }

        return $response;
    }

    public function getByFilter(?string $ogrn, ?string $capital)
    {
        $productions = $this->productionRepository->findByfilters($ogrn, $capital);
        $response = ['data' => []];

        if ($productions) {
            foreach ($productions as $production) {
                $response['data'][] = $this->prepareDataByArray($production);
            }
        }

        return $response;
    }

    public function getAllProductionsPaginated(int $page, int $peerPage): array
    {
        $productions = $this->productionRepository->findProductionsPaginated($page, $peerPage);
        $response = [];

        /** @var Production $production */
        foreach ($productions as $production) {
            $response[] = $this->prepareResponseArray($production);
        }

        $productions->setItems($response);

        return $this->prepareResponse($response, $productions);
    }

    private function prepareDataByArray(array $production): array
    {
        return [
            'id' => $production['id'],
            'title' => $production['Title'],
            'address' => $production['Address'],
            'url' => $production['url'],
            'inn' => $production['inn'],
            'ogrn' => $production['Ogrn'],
            'odkpPrimary' => []
        ];
    }

    private function prepareResponseArray(Production $production)
    {
        return [
            'id' => $production->getId(),
            'title' => $production->getTitle(),
            'address' => $production->getAddress(),
            'url' => $production->getUrl(),
            'contacts' => $this->pepareContactInfo($production->getContactInfo()),
            'inn' => $production->getInn(),
            'ogrn' => $production->getOgrn(),
            'odkpPrimary' => $this->prepareOdkpPrimary($production->getOdvkPrimary()),
            'odkpSecondary' => $this->prepareOdkpSecondary($production->getOdkvSecondary()),
            'regDate' => $production->getCompanyRegData(),
            'statutoryCapital' => $production->getStaturoryCapital(),
            'finReport' => $production->getFinReport(),
            'support' => $production->getSupport(),
        ];
    }

    private function prepareResponse(array $items, PaginationInterface $pagination): array
    {
        return [
            'data' => $items,
            'meta' => [
                'totalCount' => $pagination->getTotalItemCount(),
                'page' => $pagination->getCurrentPageNumber(),
                'perPage' => $pagination->getItemNumberPerPage()
            ]
        ];
    }

    private function pepareContactInfo(?ContactInfo $contactInfo): ?array
    {
        if (!$contactInfo) {
            return null;
        }

        return [
            'contactFio' => $contactInfo->getContactFio(),
            'directorFio' => $contactInfo->getDirectorFio(),
            'email' => $contactInfo->getContactEmail(),
            'fax' => $contactInfo->getContactFax(),
            'phone' => $contactInfo->getContactPhone()
        ];
    }

    private function prepareOdkpPrimary(?Odkv $odkv): ?array
    {
        $response = null;

        if ($odkv) {
            $response = [
                'code' => $odkv->getCode(),
                'title' => $odkv->getTitle()
            ];
        }

        return $response;
    }

    private function prepareOdkpPrimaryArray(?array $odkv): ?array
    {
        $response = null;

        if ($odkv) {
            $response = [
                'code' => $odkv['code'],
                'title' => $odkv['title']
            ];
        }

        return $response;
    }

    private function prepareOdkpSecondary(Collection $odkvs): ?array
    {
        $response = null;

        if ($odkvs->count() != 0) {
            $response = [];

            /** @var Odkv $odkv */
            foreach ($odkvs as $odkv) {
                $response[] = [
                    'code' => $odkv->getCode(),
                    'title' => $odkv->getTitle()
                ];
            }
        }

        return $response;
    }
}