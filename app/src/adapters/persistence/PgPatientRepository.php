<?php

namespace toubilib\adapters\persistence;

use PDO;
use toubilib\application\ports\spi\PatientRepositoryInterface;
use toubilib\domain\entities\Patient;

class PgPatientRepository implements PatientRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function exists(string $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM patient WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function findById(string $id): ?Patient
    {
        $stmt = $this->pdo->prepare("SELECT id, nom, prenom FROM patient WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Patient($row['id'], $row['nom'], $row['prenom']);
    }
}