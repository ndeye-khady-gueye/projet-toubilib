<?php

namespace toubilib\adapters\persistence;

use PDO;
use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\domain\entities\Praticien;

class PgPraticienRepository implements PraticienRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère la liste des praticiens avec des filtres facultatifs par spécialité et/ou nom.
     *
     * @return Praticien[]
     */
    public function getPraticiens(?string $specialite = null, ?string $nom = null): array
    {
        $sql = "SELECT p.id, p.nom, p.prenom, p.ville, p.telephone, s.libelle AS specialite 
                FROM praticien p 
                LEFT JOIN specialite s ON p.specialite_id = s.id 
                WHERE 1=1";
        $params = [];

        if (!empty($specialite)) {
            $sql .= " AND LOWER(s.libelle) = LOWER(:specialite)";
            $params['specialite'] = $specialite;
        }

        if (!empty($nom)) {
            $sql .= " AND (p.nom ILIKE :nom OR p.prenom ILIKE :nom)";
            $params['nom'] = '%' . $nom . '%';
        }

        $sql .= " ORDER BY p.nom ASC, p.prenom ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $praticiens = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $praticiens[] = $this->toEntity($row);
        }

        return $praticiens;
    }

    /**
     * Recherche un praticien par son identifiant unique.
     */
    public function findById(string $id): ?Praticien
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.id, p.nom, p.prenom, p.ville, p.telephone, s.libelle AS specialite 
             FROM praticien p 
             LEFT JOIN specialite s ON p.specialite_id = s.id 
             WHERE p.id = :id"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->toEntity($row);
    }

    /**
     * Reconstitue une entité Domaine Praticien à partir d'une ligne SQL.
     */
    private function toEntity(array $row): Praticien
    {
        return new Praticien(
            $row['id'],
            $row['nom'],
            $row['prenom'],
            $row['ville'] ?? '',
            $row['telephone'] ?? '',
            $row['specialite'] ?? ''
        );
    }
}