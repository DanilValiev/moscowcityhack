<?php

namespace App\Service\ControllerManager;

use App\Entity\Industrial;
use App\Entity\Product;
use App\Entity\Production;
use App\Repository\ProductRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ProductManager
{
    public function __construct(
        private ProductRepository $productRepository
    ) { }

    public function getAllProductionsPaginated(int $page, int $peerPage): array
    {
        $products = $this->productRepository->findProductPaginated($page, $peerPage);
        $response = [];

        /** @var Product $product */
        foreach ($products as $product) {
            $response[] = $this->prepareResponseArray($product);
        }

        return $this->prepareResponse($response, $products);
    }

    public function getByName(string $name): array
    {
        $productions = $this->productRepository->findProductionByName($name);
        $response = ['data' => []];

        if ($productions) {
            foreach ($productions as $production) {
                $response['data'][] = $this->prepareResponseArray($production);
            }
        }

        return $response;
    }

    public function getByFilter(?string $odkp, ?string $ogrn)
    {
        $productions = $this->productRepository->findByfilters($odkp, $ogrn);
        $response = ['data' => []];

        if ($productions) {
            foreach ($productions as $production) {
                $response['data'][] = $this->prepareDataByArray($production);
            }
        }

        return $response;
    }

    private function prepareResponseArray(Product $product)
    {
        return [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'createdAt' => $product->getCreatedAt(),
            'count' => $product->getAvailableCount(),
            'price' => $product->getCost(),
            'description' => $product->getDescription(),
            'gost' => $product->getGost(),
            'odkp2' => $product->getOdkp2(),
            'photo' => $product->getPhoto(),
            'tnved' => $product->getTnved(),
            'okei' => $product->getOkeiId(),
            'industry' => $this->prepareInductry($product->getIndustry()),
            'company' => $this->prepareSmallCompany($product->getCompany())
        ];
    }

    private function prepareDataByArray(array $product)
    {
        return [
            'id' => $product['id'],
            'title' => $product['Title'],
            'createdAt' => $product['created_at'],
            'gost' => $product['gost'],
            'odkp2' => $product['odkp2'],
            'photo' => $product['photo'],
            'tnved' => $product['tnved']
        ];
    }

    private function prepareSmallCompany(?Production $production)
    {
        $response = null;

        if ($production) {
            $response = [
                'id' => $production->getId(),
                'title' => $production->getTitle(),
                'ogrn' => $production->getOgrn()
            ];
        }

        return $response;
    }

    private function prepareInductry(?Industrial $industrial): ?array
    {
        $response = null;

        if ($industrial) {
            $response = [
                'id' => $industrial->getId(),
                'title' => $industrial->getTitle(),
                'photo' => $industrial->getPhoto(),
                'address' => $industrial->getAdress(),
                'uri' => $industrial->getUri()
            ];
        }

        return $response;
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
}