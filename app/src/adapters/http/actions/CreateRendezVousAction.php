<?php

namespace toubilib\adapters\http\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\application\ports\api\ServiceRendezVousInterface;
use toubilib\application\dto\CreateRdvDTO;
use InvalidArgumentException;

class CreateRendezVousAction
{
    private ServiceRendezVousInterface $serviceRendezVous;

    public function __construct(ServiceRendezVousInterface $serviceRendezVous)
    {
        $this->serviceRendezVous = $serviceRendezVous;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];

        try {
            // 1. Validation des données HTTP et création du DTO
            $dto = CreateRdvDTO::fromArray($data);

            // 2. Appel du service métier (Vérifiez si votre méthode s'appelle createRendezVous ou creerRendezVous)
            $rdvDto = $this->serviceRendezVous->createRendezVous($dto);

            // 3. Construction de la réponse JSON au format HATEOAS
            $responseData = [
                'type' => 'ressource',
                'rendez_vous' => [
                    'id' => $rdvDto->id,
                    'praticien_id' => $rdvDto->praticienId ?? $dto->praticienId,
                    'patient_id' => $rdvDto->patientId ?? $dto->patientId,
                    'date_debut' => $rdvDto->dateHeure ?? $dto->dateHeure,
                    'motif' => $rdvDto->motif ?? $dto->motif,
                    'etat' => $rdvDto->etat ?? 'planifié'
                ],
                'links' => [
                    'self' => ['href' => "/rendezvous/{$rdvDto->id}"],
                    'praticien' => ['href' => "/praticiens/" . ($rdvDto->praticienId ?? $dto->praticienId)],
                    'patient' => ['href' => "/patients/" . ($rdvDto->patientId ?? $dto->patientId)]
                ]
            ];

            $response->getBody()->write(json_encode($responseData));

            // 4. Envoi de la réponse 201 Created avec l'en-tête Location
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Location', "/rendezvous/{$rdvDto->id}")
                ->withStatus(201);

        } catch (InvalidArgumentException $e) {
            // Erreur 400 Bad Request : validation du DTO échouée (champs manquants ou format incorrect)
            $response->getBody()->write(json_encode([
                'error' => 'Bad Request',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

        } catch (\Exception $e) {
            // Erreur 422 Unprocessable Entity : règle métier non respectée (praticien indisponible, motif invalide, etc.)
            $response->getBody()->write(json_encode([
                'error' => 'Unprocessable Entity',
                'message' => "Erreur lors de la création du rendez-vous : " . $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }
    }
}