<?php

namespace toubilib\application\services;

use toubilib\application\ports\api\ServicePraticienInterface;
use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\application\dto\PraticienDTO;

class ServicePraticien implements ServicePraticienInterface
{
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository)
    {
        $this->praticienRepository = $praticienRepository;
    }

    public function getPraticiens(): array
    {
        $praticiens = $this->praticienRepository->findAll();

        return array_map(function ($praticien) {
            return new PraticienDTO(
                $praticien->getId(),
                $praticien->getNom(),
                $praticien->getPrenom(),
                $praticien->getAdresse(),
                $praticien->getTel(),
                $praticien->getSpecialite()
            );
        }, $praticiens);
    }
}