<?php

namespace toubilib\application\services;

use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\application\ports\spi\PatientRepositoryInterface;
use toubilib\application\dto\CreateRdvDTO;
use InvalidArgumentException;

class RdvValidator
{
    private PraticienRepositoryInterface $praticienRepository;
    private PatientRepositoryInterface $patientRepository;

    public function __construct(
        PraticienRepositoryInterface $praticienRepository,
        PatientRepositoryInterface $patientRepository
    ) {
        $this->praticienRepository = $praticienRepository;
        $this->patientRepository = $patientRepository;
    }

    public function validate(CreateRdvDTO $dto): void
    {
        if (!$this->praticienRepository->exists($dto->praticienId)) {
            throw new InvalidArgumentException("Le praticien spécifié n'existe pas.");
        }

        if (!$this->patientRepository->exists($dto->patientId)) {
            throw new InvalidArgumentException("Le patient spécifié n'existe pas.");
        }
    }
}