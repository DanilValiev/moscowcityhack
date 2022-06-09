<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/production')]
class ProductionController
{
    #[Route('/get', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return new JsonResponse('test');
    }
}