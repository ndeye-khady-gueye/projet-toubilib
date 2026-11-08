<?php

namespace toubilib\application\dto;

use InvalidArgumentException;

class CreateRdvDTO
{
    public readonly string $praticienId;
    public readonly string $patientId;
    public readonly string $dateHeure;
    public readonly string $motif;

    public function __construct(string $praticienId, string $patientId, string $dateHeure, string $motif)
    {
        $this->praticienId = $praticienId;
        $this->patientId = $patientId;
        $this->dateHeure = $dateHeure;
        $this->motif = $motif;
    }

    public static function fromArray(array $data): self
    {
        // 1. Contrôle de présence des champs obligatoires
        if (empty($data['praticien_id']) || empty($data['patient_id']) || empty($data['date_heure']) || empty($data['motif'])) {
            throw new InvalidArgumentException("Champs obligatoires manquants : praticien_id, patient_id, date_heure et motif sont requis.");
        }

        // 2. Contrôle du format de la date (ex: Y-m-d H:i)
        $dateFormatted = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $data['date_heure']);
        if (!$dateFormatted || $dateFormatted->format('Y-m-d H:i') !== $data['date_heure']) {
            throw new InvalidArgumentException("Format de date invalide. Le format attendu est Y-m-d H:i.");
        }

        return new self(
            trim(htmlspecialchars($data['praticien_id'])),
            trim(htmlspecialchars($data['patient_id'])),
            trim($data['date_heure']),
            trim(htmlspecialchars($data['motif']))
        );
    }
}