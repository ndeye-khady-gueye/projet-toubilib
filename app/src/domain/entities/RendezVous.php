<?php

namespace toubilib\domain\entities;

use DateTimeImmutable;
use InvalidArgumentException;
use toubilib\domain\exceptions\InvalidRendezVousStateException;

class RendezVous
{
    private string $id;
    private string $etat; // 'PLANIFIE', 'HONORE', 'ANNULE'
    private DateTimeImmutable $dateHeure;
    private ?string $raisonAnnulation;

    public function __construct(
        string $id,
        string $etat = 'PLANIFIE',
        ?DateTimeImmutable $dateHeure = null
    ) {
        $this->id = $id;
        $this->etat = $etat;
        $this->dateHeure = $dateHeure ?? new DateTimeImmutable();
        $this->raisonAnnulation = null;
    }

    public function annuler(string $raison): void
    {
        if (empty(trim($raison))) {
            throw new InvalidArgumentException("La raison d'annulation est obligatoire.");
        }

        if ($this->etat === 'ANNULE') {
            throw new InvalidRendezVousStateException("Le rendez-vous est déjà annulé.");
        }

        if ($this->etat === 'HONORE') {
            throw new InvalidRendezVousStateException("Impossible d'annuler un rendez-vous déjà honoré.");
        }

        // Contrôle de validité par rapport à la date du rendez-vous
        if ($this->dateHeure < new DateTimeImmutable()) {
            throw new InvalidRendezVousStateException("Impossible d'annuler un rendez-vous dont la date est passée.");
        }

        $this->etat = 'ANNULE';
        $this->raisonAnnulation = $raison;
    }

    public function getId(): string 
    { 
        return $this->id; 
    }

    public function getEtat(): string 
    { 
        return $this->etat; 
    }

    public function getDateHeure(): DateTimeImmutable 
    { 
        return $this->dateHeure; 
    }

    public function getRaisonAnnulation(): ?string 
    { 
        return $this->raisonAnnulation; 
    }
}