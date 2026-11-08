<?php

namespace toubilib\application\dto;

use toubilib\domain\entities\RendezVous;

class RendezVousDTO
{
    public string $id;
    public string $praticienId;
    public string $patientId;
    public string $dateHeure;
    public int $duree;
    public string $motif;
    public string $etat;

    public function __construct(
        string $id,
        string $praticienId,
        string $patientId,
        string $dateHeure,
        int $duree,
        string $motif,
        string $etat
    ) {
        $this->id = $id;
        $this->praticienId = $praticienId;
        $this->patientId = $patientId;
        $this->dateHeure = $dateHeure;
        $this->duree = $duree;
        $this->motif = $motif;
        $this->etat = $etat;
    }

    public static function fromEntity(RendezVous $rdv): self
    {
        return new self(
            $rdv->getId(),
            $rdv->getPraticien() ? $rdv->getPraticien()->getId() : '',
            $rdv->getPatient() ? $rdv->getPatient()->getId() : '',
            $rdv->getDateHeure()->format('Y-m-d H:i:s'),
            $rdv->getDureeMinutes(),
            $rdv->getMotif(),
            $rdv->getEtat()
        );
    }
}