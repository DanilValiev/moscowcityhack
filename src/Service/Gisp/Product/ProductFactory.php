<?php

namespace App\Service\Gisp\Product;

use App\Entity\Categories;
use App\Entity\Industrial;
use App\Entity\Product;
use App\Entity\Production;
use App\Repository\IndustrialRepository;
use App\Repository\ProductionRepository;
use DateTimeImmutable;

class ProductFactory
{
    public function __construct(
        private IndustrialRepository $industrialRepository,
        private ProductionRepository $productionRepository
    ) { }

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

    public function buildDetail(Product &$product, array $details)
    {
        $product
            ->setDescription($details['description'])
            ->setGost($details['gost'])
            ->setIndustry($this->getIndustry($details['industrialArea'], $details['company']))
            ->setOkeiId($details['okeiId'])
            ->setOdkp2($details['okpd2']['code'] ?? null)
            ->setTnved($details['tnved'][0]['code'] ?? null)
            ->setCharacteristics($details['characteristics'])
        ;
    }

    private function getIndustry(?array $details, array $company): ?Industrial
    {
        $industrial = null;

        if ($details) {
            $industrial = $this->industrialRepository->findOneBy(['externalId' => $details['id']]);

            if (!$industrial) {
                $industrial = new Industrial();

                $industrial
                    ->setExternalId($details['id'])
                    ->setTitle($details['name'])
                    ->setPhoto($details['photo'])
                    ->setAdress($details['address'])
                    ->setUri($details['url'])
                ;

                $production = $this->getProduction($company['ogrn']);
                $production->addIndustry($industrial);
                $industrial->addProduction($production);

                $this->industrialRepository->add($industrial, true);
            }
        }

        return $industrial;
    }

    private function getProduction(string $ogrn): Production
    {
        return $this->productionRepository->findOneBy(['Ogrn' => $ogrn]);
    }
}