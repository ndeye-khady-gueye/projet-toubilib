<?php

namespace toubilib\application\ports\api;

use toubilib\application\dto\RendezVousDTO;

interface ServiceRendezVousInterface
{
    /**
     * Annule un rendez-vous et retourne le DTO mis à jour
     * 
     * @param string $rdvId
     * @param string $raison
     * @return RendezVousDTO
     * @throws \toubilib\application\exceptions\RendezVousNotFoundException
     * @throws \toubilib\domain\exceptions\InvalidRendezVousStateException
     */
    public function annuler(string $rdvId, string $raison): RendezVousDTO;
}