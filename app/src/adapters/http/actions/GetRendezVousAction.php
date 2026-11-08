<?php

namespace toubilib\adapters\http\actions;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use toubilib\application\ports\api\ServiceRendezVousInterface;

class GetRendezVousAction
{
    private ServiceRendezVousInterface $serviceRendezVous;

    public function __construct(ServiceRendezVousInterface $serviceRendezVous)
    {
        $this->serviceRendezVous = $serviceRendezVous;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];

        $rdvDto = $this->serviceRendezVous->getRendezVousById($id);

        if (!$rdvDto) {
            throw new HttpNotFoundException($request, "Rendez-vous {$id} not found");
        }

        // Récupération sécurisée selon que c'est un DTO (propriétés/méthodes) ou une entité
        $idRdv        = is_object($rdvDto) && method_exists($rdvDto, 'getId') ? $rdvDto->getId() : ($rdvDto->id ?? $id);
        $praticienId  = is_object($rdvDto) && method_exists($rdvDto, 'getPraticienId') ? $rdvDto->getPraticienId() : ($rdvDto->praticienId ?? $rdvDto->praticien_id ?? '');
        $patientId    = is_object($rdvDto) && method_exists($rdvDto, 'getPatientId') ? $rdvDto->getPatientId() : ($rdvDto->patientId ?? $rdvDto->patient_id ?? '');
        $etat         = is_object($rdvDto) && method_exists($rdvDto, 'getEtat') ? $rdvDto->getEtat() : ($rdvDto->etat ?? 'PLANIFIE');
        
        $dateRaw      = is_object($rdvDto) && method_exists($rdvDto, 'getDateHeure') ? $rdvDto->getDateHeure() : ($rdvDto->dateHeure ?? null);
        $dateDebut    = ($dateRaw instanceof \DateTimeInterface) ? $dateRaw->format('d/m/Y H:i') : (string) $dateRaw;

        $data = [
            'type' => 'ressource',
            'rendez_vous' => [
                'id'           => (string) $idRdv,
                'praticien_id' => (string) $praticienId,
                'patient_id'   => (string) $patientId,
                'date_debut'   => $dateDebut,
                'duree'        => 30,
                'motif_visite' => 'Consultation',
                'etat'         => (string) $etat
            ],
            'links' => [
                'self'      => ['href' => "/rdvs/{$idRdv}"],
                'praticien' => ['href' => "/praticiens/{$praticienId}"],
                'patient'   => ['href' => "/patients/{$patientId}"],
                'annuler'   => ['href' => "/rdvs/{$idRdv}/annuler"],
                'honorer'   => ['href' => "/rdvs/{$idRdv}/honorer"],
                'ignorer'   => ['href' => "/rdvs/{$idRdv}/ignorer"]
            ]
        ];

        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}