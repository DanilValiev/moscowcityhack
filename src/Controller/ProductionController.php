<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/production')]
class ProductionController
{

    #[Route('/get', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $response = 'test';
        return new JsonResponse($response);
    }
}