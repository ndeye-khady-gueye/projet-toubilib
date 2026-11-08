<?php

use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// 1. Instanciation du conteneur PHP-DI
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/dependencies.php');
$container = $containerBuilder->build();

// 2. Création de l'application Slim avec le conteneur
AppFactory::setContainer($container);
$app = AppFactory::create();

// 3. Middlewares Slim essentiels
$app->addBodyParsingMiddleware();

// --- MIDDLEWARE CORS (TD1.4 Exercice 4) ---
$app->add(function (Request $request, $handler): Response {
    $response = $handler->handle($request);
    
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
});

// Traitement des requêtes Preflight OPTIONS (navigateurs web)
$app->options('/{routes:.+}', function (Request $request, Response $response): Response {
    return $response;
});
// ------------------------------------------

$app->addRoutingMiddleware();

// Activer le middleware d'erreur pour capturer les warnings/erreurs PHP en JSON
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// 4. Chargement des routes
$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

// 5. Exécution de l'application
$app->run();