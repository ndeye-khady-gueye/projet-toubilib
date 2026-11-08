<?php

namespace toubilib\application\services;

use DateTimeImmutable;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use toubilib\application\dto\CreateRdvDTO;
use toubilib\application\dto\RendezVousDTO;
use toubilib\application\ports\api\ServiceRendezVousInterface;
use toubilib\application\ports\spi\PatientRepositoryInterface;
use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\domain\entities\RendezVous;

class ServiceRendezVous implements ServiceRendezVousInterface
{
    private PraticienRepositoryInterface $praticienRepository;
    private PatientRepositoryInterface $patientRepository;
    private RendezVousRepositoryInterface $rendezVousRepository;

    public function __construct(
        PraticienRepositoryInterface $praticienRepository,
        PatientRepositoryInterface $patientRepository,
        RendezVousRepositoryInterface $rendezVousRepository
    ) {
        $this->praticienRepository = $praticienRepository;
        $this->patientRepository = $patientRepository;
        $this->rendezVousRepository = $rendezVousRepository;
    }

    public function createRendezVous(CreateRdvDTO $dto): RendezVousDTO
    {
        // 1. Chargement du praticien et du patient
        $praticien = $this->praticienRepository->findById($dto->praticienId);
        if (!$praticien) {
            throw new InvalidArgumentException("Praticien introuvable");
        }

        $patient = $this->patientRepository->findById($dto->patientId);
        if (!$patient) {
            throw new InvalidArgumentException("Patient introuvable");
        }

        // 2. Conversion de la date
        try {
            $dateHeure = new DateTimeImmutable($dto->dateHeure);
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Format de date/heure invalide");
        }

        if ($dateHeure <= new DateTimeImmutable()) {
            throw new InvalidArgumentException("La date du rendez-vous doit être dans le futur");
        }

        // 3. Chargement des RDV du praticien pour la journée et affectation à son agenda
        $rdvsExistants = $this->rendezVousRepository->findByPraticienAndDate($dto->praticienId, $dateHeure);
        $praticien->setRendezVousList($rdvsExistants);

        // 4. Génération de l'ID et délégation complète de la validation/création à l'entité Domaine
        $rdvId = Uuid::uuid4()->toString();
        $rdv = RendezVous::validerEtCreer($rdvId, $praticien, $patient, $dateHeure, $dto->motif);

        // 5. Persistance en base de données
        $this->rendezVousRepository->save($rdv);

        return RendezVousDTO::fromEntity($rdv);
    }

    public function annuler(string $rdvId, string $raison): RendezVousDTO
    {
        // 1. Récupération du rendez-vous
        $rdv = $this->rendezVousRepository->findById($rdvId);
        if (!$rdv) {
            throw new \Exception("Rendez-vous $rdvId introuvable.");
        }

        // 2. Délégation des règles métier à l'entité (raison obligatoire, statut modifiable, date non passée)
        $rdv->annuler($raison);

        // 3. Mise à jour en base de données via le repository
        $this->rendezVousRepository->update($rdv);

        // 4. Retour du DTO mis à jour
        return RendezVousDTO::fromEntity($rdv);
    }

    public function getRendezVousById(string $id): RendezVousDTO
    {
        $rdv = $this->rendezVousRepository->findById($id);
        
        if (!$rdv) {
            throw new \Exception("Rendez-vous $id introuvable.");
        }

        return RendezVousDTO::fromEntity($rdv);
    }
}