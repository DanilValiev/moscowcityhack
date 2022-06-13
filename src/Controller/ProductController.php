<?php

namespace App\Controller;

use App\Service\ControllerManager\ProductManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/product')]
class ProductController
{
    public function __construct(
        private ProductManager $productionManager
    ) { }

    #[Route('/get', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);;
        $perPage = $request->query->get('perPage', 25);;

        $response = $this->productionManager->getAllProductionsPaginated($page, $perPage);

        return new JsonResponse($response, 200, [
            'Access-Control-Allow-Origin' => '*'
        ]);
    }

    #[Route('/search/{name}', methods: ['GET'])]
    public function getByName(string $name): JsonResponse
    {
        $response = $this->productionManager->getByName($name);

        return new JsonResponse($response, 200, [
            'Access-Control-Allow-Origin' => '*'
        ]);
    }

    #[Route('/filter', methods: ['GET'])]
    public function getByFilters(Request $request): JsonResponse
    {
        $odkp = $request->query->get('odkp', 0);;
        $capital = $request->query->get('capital', 0);;

        $response = $this->productionManager->getByFilter($odkp, $capital);

        return new JsonResponse($response, 200, [
            'Access-Control-Allow-Origin' => '*'
        ]);
    }
}