<?php

namespace App\Service\Gisp\Production;

use App\Entity\ContactInfo;
use App\Entity\Odkv;
use App\Entity\Production;
use App\Entity\Region;
use App\Repository\OdkvRepository;

class ProductionFactory
{

    public function __construct(
        private OdkvRepository $odkvRepository
    ) { }

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

    public function buildDetailData(Production &$production, array $detail)
    {
//        print_r($production->getTitle());
//        die();
        $preparedData = $this->prepareDetailData($detail);

        if (!$production->getOgrn()) {
            $production->setOgrn($preparedData['ogrn']);
        }
        if (!$production->getAddress()) {
            $production->setAddress($preparedData['address']);
        }
        if (!$production->getCompanyRegData()) {
            $production->setCompanyRegData($preparedData['creationDate']);
        }
        if (!$production->getSupport()) {
            $production->setSupport($preparedData['support']);
        }
        if (!$production->getCompanyRegData()) {
            $production->setCompanyRegData($preparedData['creationDate']);
        }
        if (!$production->getStaturoryCapital()) {
            $production->setStaturoryCapital($preparedData['capital']);
        }
        if (!$production->getFinReport()) {
            $production->setFinReport($preparedData['fnReport']);
        }
        if (!$production->getOdvkPrimary()) {
            $odkv = $this->prepareOdkv($preparedData['okvd'], true);

            $production->setOdvkPrimary($odkv['primary']);
        }
        if (!$production->getOdkvSecondary()->count()) {
            $odkvs = $this->prepareOdkv($preparedData['okvd'], false);

            foreach ($odkvs as $odkv) {
                if ($odkv) {
                    $production->addOdkvSecondary($odkv[0]);
                }
            }
        }
    }

    private function prepareOdkv(array $odkv, bool $primary): array
    {
        $odkvModelPrimary = null;
        $odkvSecArray = [];

        if ($primary) {
            if (isset($odkv['primary'])) {
                $odkvModelPrimary = $this->odkvRepository->findOneBy(['code' => $odkv['primary']['code']]);

                if (!$odkvModelPrimary) {
                    $odkvModelPrimary = new Odkv();

                    $odkvModelPrimary
                        ->setTitle($odkv['primary']['title'])
                        ->setCode($odkv['primary']['code'])
                    ;

                    $this->odkvRepository->add($odkvModelPrimary, true);
                }
            }
        }
        else {
            if (isset($odkv['secondary'])) {
                foreach ($odkv['secondary'] as $odkvSec) {
                    $odkvModelSec = $this->odkvRepository->findOneBy(['code' => $odkvSec['code']]);

                    if (!$odkvModelSec) {
                        $odkvModelSec = new Odkv();

                        $odkvModelSec
                            ->setCode($odkvSec['code'])
                            ->setTitle($odkvSec['title'])
                        ;

                        $this->odkvRepository->add($odkvModelSec, true);
                    }

                    $odkvSecArray[] = $odkvModelSec;
                }
            }
        }

        return [
            'primary' => $odkvModelPrimary,
            'sec' => $odkvSecArray
        ];
    }

    private function prepareDetailData(array $detail): array
    {
        $basicOrgSved = $detail['СвЮЛ']['@attributes'] ?? $detail['СвИП']['@attributes'];
        $basicOrgAddress = $detail['СвЮЛ']['СвАдресЮЛ'] ?? null;

        $basicOkvdPlace = null;
        if (isset($detail['СвЮЛ']['СвОКВЭД']) || isset($detail['СвИП']['СвОКВЭД'])) {
            $basicOkvdPlace = $detail['СвЮЛ']['СвОКВЭД'] ?? $detail['СвИП']['СвОКВЭД'];
        }

        $capital = null;
        if (isset($detail['СвЮЛ']['СвУстКап']['@attributes']['СумКап'])) {
            $capital = $detail['СвЮЛ']['СвУстКап']['@attributes']['СумКап'];
        }

        $directorFio = null;
        if (isset($detail['СвЮЛ']['СведДолжнФЛ']['СвФЛ']['@attributes'])) {
            $fioArray = $detail['СвЮЛ']['СведДолжнФЛ']['СвФЛ']['@attributes'];

            if (!isset($fioArray['Отчество'])) {
                $fioArray['Отчество'] = '';
            }

            $directorFio = "{$fioArray['Фамилия']} {$fioArray['Имя']} {$fioArray['Отчество']}";
        }

        return [
            'ogrn' => $basicOrgSved['ОГРН'] ?? $basicOrgSved['ОГРНИП'],
            'creationDate' => $basicOrgSved['ДатаОГРН'] ?? $basicOrgSved['ДатаОГРНИП'],
            'address' => $this->prepareAddress($basicOrgAddress),
            'capital' => $capital,
            'directorFio' => $directorFio,
            'okvd' => $this->prepareOkvd($basicOkvdPlace),
            'fnReport' => $this->prepareFn($detail['fin'] ?? null),
            'support' => $this->prepareFnSupport($detail['fin']['support'] ?? null)
        ];
    }

    private function prepareFn(?array $data): array
    {
        $response = [];

        if ($data) {
            foreach ($data as $key => $years) {
                if ($key[0] == 'y') {
                    $response[$key] = $years['@attributes'];
                }
            }
        }

        return $response;
    }

    private function prepareFnSupport(?array $data): array
    {
        $response = [];

        if ($data) {
            foreach ($data as $support) {
                if (!isset($support['@attributes']) || $support['@attributes']['s_type'] != 1) {
                    continue ;
                }

                $response[] = [
                    's' => $support['@attributes']['s'],
                    'accept_date' => $support['@attributes']['accept_date']
                ];
            }
        }

        return $response;
    }

    private function prepareOkvd(?array $data): array
    {
        if (!$data) {
            return [];
        }
        $okvdPrimary = [];

        if (isset($data['СвОКВЭДОсн']['@attributes'])) {
            $okvdPrimary = [
                'code' => $data['СвОКВЭДОсн']['@attributes']['КодОКВЭД'],
                'title' => $data['СвОКВЭДОсн']['@attributes']['НаимОКВЭД']
            ];
        }

        $okvdSecondary = [];
        if (isset($data['СвОКВЭДДоп'])) {
            foreach ($data['СвОКВЭДДоп'] as $secOkvd) {
                if (isset($secOkvd['@attributes']['КодОКВЭД']) && isset($secOkvd['@attributes']['НаимОКВЭД'])) {
                    $okvdSecondary[] = [
                        'code' => $secOkvd['@attributes']['КодОКВЭД'],
                        'title' => $secOkvd['@attributes']['НаимОКВЭД']
                    ];
                }
            }
        }

        return [
            'primary' => $okvdPrimary,
            'secondary' => $okvdSecondary
        ];
    }

    private function prepareAddress(?array $data): ?string
    {
        if (!$data) {
            return '';
        }

        $city = '';
        if (isset($data['Регион']['@attributes']['НаимРегион'])) {
            $city = $data['Регион']['@attributes']['НаимРегион'];
        }

        $street = '';
        if (isset($data['Улица']['@attributes']['НаимУлица'])) {
            $street = $data['Улица']['@attributes']['НаимУлица'];
        }

        $house = '';
        if (isset($data['АдресРФ']['@attributes']['Дом'])) {
            $street = $data['АдресРФ']['@attributes']['Дом'];
        }

        return "{$city} {$street} {$house}";
    }

    private function buildBasicData(array $productionBasic): Production
    {
        $productionModel = new Production();

        $productionModel
            ->setTitle($productionBasic['org_name'] ?? $productionBasic['name'])
            ->setOgrn($productionBasic['org_ogrn'] ?? $productionBasic['ogrn'])
            ->setInn($productionBasic['org_inn'] ?? $productionBasic['inn'])
            ->setAddress($productionBasic['org_addr'] ?? null)
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