<?php

namespace App\Service\Gisp;

use App\Entity\ContactInfo;
use App\Entity\Production;
use App\Entity\Region;

class ProductionFactory
{
    public function build(array $productionBasic, ?array $productionDetails): Production
    {
        $productionModel = $this->buildBasicData($productionBasic);

        if ($productionDetails) {
            $contactModel = $this->buildContactData($productionDetails);
            $regionModel = $this->buildRegionData($productionDetails['region']);

            $productionModel
                ->setContactInfo($contactModel)
                ->setRegion($regionModel)
            ;
        }

        return $productionModel;
    }

    private function buildBasicData(array $productionBasic): Production
    {
        $productionModel = new Production();

        $productionModel
            ->setTitle($productionBasic['org_name'])
            ->setOgrn($productionBasic['org_ogrn'])
            ->setInn($productionBasic['org_inn'])
            ->setAddress($productionBasic['org_addr'])
        ;

        return $productionModel;
    }

    private function buildContactData(array $productionDetails): ContactInfo
    {
        $contactInfoModel = new ContactInfo();
        $site = $productionDetails['contact_email'] ? explode('@', $productionDetails['contact_email']) : null;

        $contactInfoModel
            ->setContactFio($productionDetails['contact_fio'])
            ->setDirectorFio($productionDetails['director_fio'])
            ->setContactPhone($productionDetails['contact_phone'])
            ->setContactEmail($productionDetails['contact_email'])
            ->setSite($site[1] ?? null)
        ;

        return $contactInfoModel;
    }

    private function buildRegionData(?array $productionRegion): ?Region
    {
        $regionModel = null;

        if ($productionRegion) {
            $regionModel = new Region();

            $regionModel
                ->setTitle($productionRegion['name'])
                ->setCode($productionRegion['code'])
                ->setAlphacode($productionRegion['alphacode'])
            ;
        }

        return $regionModel;
    }
}