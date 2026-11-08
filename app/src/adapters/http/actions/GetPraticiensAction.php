<?php

namespace toubilib\adapters\http\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\application\ports\api\ServicePraticienInterface;
use Throwable;

class GetPraticiensAction
{
    private ServicePraticienInterface $servicePraticien;

    public function __construct(ServicePraticienInterface $servicePraticien)
    {
        $this->servicePraticien = $servicePraticien;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        // 1. Récupération des paramètres de filtrage dans l'URL (?specialite=...&nom=...)
        $queryParams = $request->getQueryParams();
        $specialite = $queryParams['specialite'] ?? null;
        $nom = $queryParams['nom'] ?? null;

        try {
            // 2. Appel du service avec les filtres facultatifs
            $praticiensDTO = $this->servicePraticien->getPraticiens($specialite, $nom);

            // 3. Transformation des DTOs et enrichissement HATEOAS
            $praticiensData = array_map(function ($dto) {
                return [
                    'id'         => $dto->id,
                    'nom'        => $dto->nom,
                    'prenom'     => $dto->prenom,
                    'specialite' => $dto->specialite,
                    'adresse'    => $dto->adresse,
                    'tel'        => $dto->tel,
                    'links'      => [
                        'self'       => ['href' => "/praticiens/{$dto->id}"],
                        'rdv_agenda' => ['href' => "/praticiens/{$dto->id}/rendezvous"]
                    ]
                ];
            }, $praticiensDTO);

            // 4. Construction de la réponse JSON au format collection REST
            $payload = json_encode([
                'type'       => 'collection',
                'count'      => count($praticiensData),
                'praticiens' => $praticiensData
            ]);

            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (Throwable $e) {
            $response->getBody()->write(json_encode([
                'error'   => 'Internal Server Error',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}