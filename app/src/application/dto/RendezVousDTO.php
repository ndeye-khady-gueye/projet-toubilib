<?php

namespace toubilib\application\dto;

use toubilib\domain\entities\RendezVous;

class RendezVousDTO
{
    public string $id;
    public string $etat;
    public ?string $raisonAnnulation;

    public function __construct(string $id, string $etat, ?string $raisonAnnulation = null)
    {
        $this->id = $id;
        $this->etat = $etat;
        $this->raisonAnnulation = $raisonAnnulation;
    }

    /**
     * Méthode fabrique permettant de créer un DTO à partir d'une entité du Domaine
     */
    public static function fromEntity(RendezVous $rdv): self
    {
        return new self(
            $rdv->getId(),
            $rdv->getEtat(),
            $rdv->getRaisonAnnulation()
        );
    }
}