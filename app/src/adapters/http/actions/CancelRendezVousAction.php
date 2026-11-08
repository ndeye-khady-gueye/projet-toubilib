<?php

namespace toubilib\adapters\http\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\application\ports\api\ServiceRendezVousInterface;
use toubilib\application\exceptions\RendezVousNotFoundException;
use toubilib\domain\exceptions\InvalidRendezVousStateException;
use InvalidArgumentException;

class CancelRendezVousAction
{
    private ServiceRendezVousInterface $serviceRendezVous;

    public function __construct(ServiceRendezVousInterface $serviceRendezVous)
    {
        $this->serviceRendezVous = $serviceRendezVous;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? '';
        $body = $request->getParsedBody() ?? [];
        $raison = $body['raison'] ?? '';

        try {
            $dto = $this->serviceRendezVous->annuler($id, $raison);

            $payload = json_encode([
                'type' => 'ressource',
                'rendez_vous' => [
                    'id'                => $dto->id,
                    'etat'              => $dto->etat,
                    'raison_annulation' => $dto->raisonAnnulation,
                ],
                'links' => [
                    'self' => ['href' => "/rendezvous/{$dto->id}"]
                ]
            ]);

            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (InvalidArgumentException $e) {
            // 400 Bad Request : Raison d'annulation manquante ou invalide
            return $this->jsonError($response, $e->getMessage(), 400);

        } catch (RendezVousNotFoundException $e) {
            // 404 Not Found : Rendez-vous inexistant
            return $this->jsonError($response, $e->getMessage(), 404);

        } catch (InvalidRendezVousStateException $e) {
            // 422 Unprocessable Entity : État incompatible (déjà annulé, honoré, date passée)
            return $this->jsonError($response, $e->getMessage(), 422);

        } catch (\Throwable $e) {
            // 500 Internal Server Error
            return $this->jsonError($response, "Erreur serveur : " . $e->getMessage(), 500);
        }
    }

    private function jsonError(Response $response, string $message, int $code): Response
    {
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($code);
    }
}