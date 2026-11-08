<?php

namespace toubilib\application\dto;

use toubilib\domain\entities\Praticien;

class PraticienDTO
{
    public string $id;
    public string $nom;
    public string $prenom;
    public string $adresse;
    public string $tel;
    public string $specialite;

    public function __construct(
        string $id,
        string $nom,
        string $prenom,
        string $adresse,
        string $tel,
        string $specialite
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
        $this->specialite = $specialite;
    }

    /**
     * Crée une instance de PraticienDTO à partir d'une entité Domaine Praticien.
     */
    public static function fromEntity(Praticien $praticien): self
    {
        return new self(
            $praticien->getId(),
            $praticien->getNom(),
            $praticien->getPrenom(),
            $praticien->getAdresse(),
            $praticien->getTel(),
            $praticien->getSpecialite()
        );
    }
}