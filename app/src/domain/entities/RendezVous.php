<?php

namespace toubilib\domain\entities;

use DateTimeImmutable;
use InvalidArgumentException;
use toubilib\domain\exceptions\InvalidRendezVousStateException;

class RendezVous
{
    private string $id;
    private ?Praticien $praticien;
    private ?Patient $patient;
    private DateTimeImmutable $dateHeure;
    private string $motif;
    private int $dureeMinutes;
    private string $etat; // 'PLANIFIE', 'HONORE', 'ANNULE'
    private ?string $raisonAnnulation;

    public function __construct(
        string $id,
        ?Praticien $praticien = null,
        ?Patient $patient = null,
        ?DateTimeImmutable $dateHeure = null,
        string $motif = 'consultation',
        int $dureeMinutes = 30,
        string $etat = 'PLANIFIE',
        ?string $raisonAnnulation = null
    ) {
        $this->id = $id;
        $this->praticien = $praticien;
        $this->patient = $patient;
        $this->dateHeure = $dateHeure ?? new DateTimeImmutable();
        $this->motif = $motif;
        $this->dureeMinutes = $dureeMinutes;
        $this->etat = $etat;
        $this->raisonAnnulation = $raisonAnnulation;
    }

    /**
     * Exercice 2 Étape 2 : Teste si ce RDV chevauche un autre RDV
     * Formule : (DébutA < FinB) ET (FinA > DébutB)
     */
    public function chevauche(RendezVous $autreRdv): bool
    {
        if ($this->etat === 'ANNULE' || $autreRdv->getEtat() === 'ANNULE') {
            return false;
        }

        $debutA = $this->dateHeure;
        $finA = $this->dateHeure->modify("+{$this->dureeMinutes} minutes");

        $debutB = $autreRdv->getDateHeure();
        $finB = $autreRdv->getDateHeure()->modify("+{$autreRdv->getDureeMinutes()} minutes");

        return ($debutA < $finB) && ($finA > $debutB);
    }

    /**
     * Exercice 2 Étape 1 & 2 : Méthode statique de validation-création de RDV
     */
    public static function validerEtCreer(
        string $id,
        Praticien $praticien,
        Patient $patient,
        DateTimeImmutable $dateHeure,
        string $motif
    ): self {
        if (!$praticien->validerMotif($motif)) {
            throw new InvalidArgumentException("Le motif '$motif' n'est pas valide pour ce praticien.");
        }

        if (!$praticien->estDateHeureValide($dateHeure)) {
            throw new InvalidArgumentException("La date/heure demandée est hors des créneaux d'ouverture du praticien.");
        }

        $duree = $praticien->getDureeMotif($motif);
        $nouveauRdv = new self($id, $praticien, $patient, $dateHeure, $motif, $duree, 'PLANIFIE');

        // Vérification de la disponibilité (aucun chevauchement)
        $praticien->verifierDisponibilite($nouveauRdv);

        return $nouveauRdv;
    }

    /**
     * Fonctionnalité 6 : Annulation d'un rendez-vous
     */
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

        if ($this->dateHeure < new DateTimeImmutable()) {
            throw new InvalidRendezVousStateException("Impossible d'annuler un rendez-vous dont la date est passée.");
        }

        $this->etat = 'ANNULE';
        $this->raisonAnnulation = $raison;
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getPraticien(): ?Praticien { return $this->praticien; }
    public function getPatient(): ?Patient { return $this->patient; }
    public function getDateHeure(): DateTimeImmutable { return $this->dateHeure; }
    public function getMotif(): string { return $this->motif; }
    public function getDureeMinutes(): int { return $this->dureeMinutes; }
    public function getEtat(): string { return $this->etat; }
    public function getRaisonAnnulation(): ?string { return $this->raisonAnnulation; }
}