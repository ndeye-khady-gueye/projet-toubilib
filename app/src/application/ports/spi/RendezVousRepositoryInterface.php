<?php

namespace toubilib\application\ports\spi;

use DateTimeImmutable;
use toubilib\domain\entities\RendezVous;

interface RendezVousRepositoryInterface
{
    public function findById(string $id): ?RendezVous;
    public function findByPraticienAndDate(string $praticienId, DateTimeImmutable $date): array;
    public function save(RendezVous $rdv): void;
}


