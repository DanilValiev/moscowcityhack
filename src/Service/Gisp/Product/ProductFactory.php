<?php

namespace App\Service\Gisp\Product;

use App\Entity\Categories;
use App\Entity\Product;
use App\Entity\Production;
use DateTimeImmutable;

class ProductFactory
{
    public function build(array $details, Categories $category, Production $production): Product
    {
        $productModel = new Product();

        $productModel
            ->setExternalId($details['id'])
            ->setTitle($details['name'])
            ->setAvailableCount($details['availableCount'])
            ->setCost($details['cost'])
            ->setCreatedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $details['createdAt']))
            ->setPhoto($details['photoUrl'])
            ->setCategory($category)
            ->setCompany($production)
        ;

        return $productModel;
    }
}