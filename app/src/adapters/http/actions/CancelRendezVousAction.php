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
                'status' => 'success',
                'data'   => [
                    'id'                => $dto->id,
                    'etat'              => $dto->etat,
                    'raison_annulation' => $dto->raisonAnnulation,
                ]
            ]);

            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (RendezVousNotFoundException $e) {
            return $this->jsonError($response, $e->getMessage(), 404);
        } catch (InvalidRendezVousStateException | InvalidArgumentException $e) {
            return $this->jsonError($response, $e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->jsonError($response, $e->getMessage(), 500);
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