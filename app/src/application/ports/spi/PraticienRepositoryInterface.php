<?php

namespace toubilib\application\ports\spi;

use toubilib\domain\entities\Praticien;

interface PraticienRepositoryInterface
{
    /**
     * Récupère la liste des praticiens avec des filtres optionnels par spécialité et nom.
     *
     * @param string|null $specialite Filtre exact ou partiel sur la spécialité
     * @param string|null $nom Filtre partiel sur le nom ou prénom
     * @return Praticien[]
     */
    public function getPraticiens(?string $specialite = null, ?string $nom = null): array;

    /**
     * Recherche un praticien par son identifiant unique.
     *
     * @param string $id
     * @return Praticien|null
     */
    public function findById(string $id): ?Praticien;
}