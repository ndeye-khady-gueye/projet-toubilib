<?php

namespace toubilib\domain\entities;

use DateTimeImmutable;
use InvalidArgumentException;

class Praticien
{
    private string $id;
    private string $nom;
    private string $prenom;
    private string $adresse;
    private string $tel;
    private string $specialite;

    /** @var RendezVous[] */
    private array $rendezVousList = [];

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

    // Getters
    public function getId(): string { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getAdresse(): string { return $this->adresse; }
    public function getTel(): string { return $this->tel; }
    public function getSpecialite(): string { return $this->specialite; }

    // Gestion de la liste des rendez-vous
    public function setRendezVousList(array $rdvs): void
    {
        $this->rendezVousList = $rdvs;
    }

    public function getRendezVousList(): array
    {
        return $this->rendezVousList;
    }

    /**
     * Exercice 2 Étape 1.1 : Vérifier si le motif est accepté
     */
    public function validerMotif(string $motif): bool
    {
        $motifsValides = ['consultation', 'suivi', 'urgence', 'bilan'];
        return in_array(strtolower(trim($motif)), $motifsValides, true);
    }

    /**
     * Exercice 2 Étape 1.2 : Calculer la durée du RDV (en minutes) selon le motif
     */
    public function getDureeMotif(string $motif): int
    {
        if (!$this->validerMotif($motif)) {
            throw new InvalidArgumentException("Motif de rendez-vous non valide pour ce praticien.");
        }

        return match (strtolower(trim($motif))) {
            'urgence' => 15,
            'consultation', 'suivi' => 30,
            'bilan' => 45,
            default => 30,
        };
    }

    /**
     * Exercice 2 Étape 1.3 : Vérifier que la date et l'heure sont possibles (ouvrable + horaires)
     */
    public function estDateHeureValide(DateTimeImmutable $dateHeure): bool
    {
        $jourSemaine = (int) $dateHeure->format('N');
        if ($jourSemaine === 7) { // Dimanche fermé
            return false;
        }

        $heure = (int) $dateHeure->format('H');
        return $heure >= 8 && $heure < 19;
    }

    /**
     * Exercice 2 Étape 2.1 : Parcourt les RDVs du praticien et lève une exception s'il y a chevauchement
     */
    public function verifierDisponibilite(RendezVous $demandeRdv): void
    {
        foreach ($this->rendezVousList as $rdvExistant) {
            if ($demandeRdv->chevauche($rdvExistant)) {
                throw new InvalidArgumentException("Le praticien n'est pas disponible à cet horaire (chevauchement avec un rendez-vous existant).");
            }
        }
    }
}