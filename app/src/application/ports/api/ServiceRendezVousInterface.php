<?php

namespace toubilib\application\ports\api;

use toubilib\application\dto\CreateRdvDTO;
use toubilib\application\dto\RendezVousDTO;

interface ServiceRendezVousInterface
{
    /**
     * Crée un nouveau rendez-vous
     */
    public function createRendezVous(CreateRdvDTO $dto): RendezVousDTO;

    /**
     * Annule un rendez-vous et retourne le DTO mis à jour
     * 
     * @param string $rdvId
     * @param string $raison
     * @return RendezVousDTO
     * @throws \toubilib\application\exceptions\RendezVousNotFoundException
     * @throws \toubilib\domain\exceptions\InvalidRendezVousStateException
     * 
     */
    public function annuler(string $rdvId, string $raison): RendezVousDTO;
    /**
     * Récupère un rendez-vous par son ID
     * 
     * @param string $id
     * @return RendezVousDTO
     * @throws \Exception si le rendez-vous n'existe pas
     */
    public function getRendezVousById(string $id): RendezVousDTO;
}