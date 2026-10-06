<?php

namespace toubilib\adapters\persistence;

use PDO;
use DateTimeImmutable;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\domain\entities\RendezVous;

class RendezVousRepository implements RendezVousRepositoryInterface
{
    private PDO $pdo;

    // Correspondance BDD (entier) <-> Domaine (chaîne)
    private const DB_TO_DOMAIN_STATUS = [
        1 => 'PLANIFIE',
        2 => 'HONORE',
        3 => 'ANNULE',
        4 => 'ANNULE',
    ];

    private const DOMAIN_TO_DB_STATUS = [
        'PLANIFIE' => 1,
        'HONORE'   => 2,
        'ANNULE'   => 3,
    ];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $id): ?RendezVous
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, status AS statut, date_heure_debut FROM rdv WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        // 1. Conversion de l'entier BDD (1, 2, 3...) en statut Domaine ('PLANIFIE', 'ANNULE'...)
        $rawStatus = (int) $row['statut'];
        $etatDomaine = self::DB_TO_DOMAIN_STATUS[$rawStatus] ?? (string) $rawStatus;

        $dateHeure = isset($row['date_heure_debut']) && $row['date_heure_debut'] !== null
            ? new DateTimeImmutable($row['date_heure_debut'])
            : new DateTimeImmutable('+1 day');

        return new RendezVous(
            (string) $row['id'],
            $etatDomaine,
            $dateHeure
        );
    }

    public function save(RendezVous $rdv): void
    {
        $etatDomaine = $rdv->getEtat();

        // 2. Conversion du statut Domaine ('ANNULE') en entier BDD (3)
        $statusInt = self::DOMAIN_TO_DB_STATUS[$etatDomaine] 
            ?? (is_numeric($etatDomaine) ? (int) $etatDomaine : 3);

        $stmt = $this->pdo->prepare(
            'UPDATE rdv 
             SET status = :statut, 
                 motif_visite = :raison 
             WHERE id = :id'
        );

        $stmt->execute([
            'statut' => $statusInt,
            'raison' => $rdv->getRaisonAnnulation(),
            'id'     => $rdv->getId(),
        ]);
    }
}