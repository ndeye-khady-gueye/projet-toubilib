<?php

namespace toubilib\application\ports\api;

use toubilib\application\dto\PraticienDTO;

interface ServicePraticienInterface
{
    /**
     * Récupère la liste des praticiens avec des filtres optionnels.
     *
     * @param string|null $specialite Filtre par spécialité
     * @param string|null $nom Filtre par nom ou mot-clé
     * @return PraticienDTO[]
     */
    public function getPraticiens(?string $specialite = null, ?string $nom = null): array;

    /**
     * Récupère les détails d'un praticien par son ID.
     */
    public function getPraticienById(string $id): PraticienDTO;
}