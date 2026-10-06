<?php

namespace toubilib\application\usecases;

use toubilib\application\ports\api\ServiceRendezVousInterface;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\application\dto\RendezVousDTO;
use toubilib\application\exceptions\RendezVousNotFoundException;

class ServiceRendezVous implements ServiceRendezVousInterface
{
    private RendezVousRepositoryInterface $repository;

    public function __construct(RendezVousRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function annuler(string $rdvId, string $raison): RendezVousDTO
    {
        $rdv = $this->repository->findById($rdvId);

        if (!$rdv) {
            throw new RendezVousNotFoundException("Rendez-vous introuvable : $rdvId");
        }

        $rdv->annuler($raison);
        $this->repository->save($rdv);

        return RendezVousDTO::fromEntity($rdv);
    }
}