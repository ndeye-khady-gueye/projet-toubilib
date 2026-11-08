<?php

namespace toubilib\application\services;

use toubilib\application\dto\PraticienDTO;
use toubilib\application\ports\api\ServicePraticienInterface;
use toubilib\application\ports\spi\PraticienRepositoryInterface;
use InvalidArgumentException;

class ServicePraticien implements ServicePraticienInterface
{
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository)
    {
        $this->praticienRepository = $praticienRepository;
    }

    /**
     * Récupère la liste filtrée des praticiens sous forme de DTOs.
     *
     * @return PraticienDTO[]
     */
    public function getPraticiens(?string $specialite = null, ?string $nom = null): array
    {
        $praticiens = $this->praticienRepository->getPraticiens($specialite, $nom);

        return array_map(function ($praticien) {
            return PraticienDTO::fromEntity($praticien);
        }, $praticiens);
    }

    /**
     * Récupère un praticien unique par son identifiant.
     */
    public function getPraticienById(string $id): PraticienDTO
    {
        $praticien = $this->praticienRepository->findById($id);

        if (!$praticien) {
            throw new InvalidArgumentException("Praticien avec l'ID $id introuvable.");
        }

        return PraticienDTO::fromEntity($praticien);
    }
}