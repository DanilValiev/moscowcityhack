<?php

namespace App\Controller;

use App\Repository\ProductionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/production')]
class ProductionController
{
    public function __construct(
        private ProductionRepository $productionRepository
    ) {}

    #[Route('/get', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $production = $this->productionRepository->findOneBy(['id' => 3]);
        print_r($production->getContactInfo()->getContactFio());
        die ;

        return new JsonResponse('test');
    }
}