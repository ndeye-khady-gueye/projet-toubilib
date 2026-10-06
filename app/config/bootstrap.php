<?php

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Chargement des dépendances si le fichier existe
if (file_exists(__DIR__ . '/dependencies.php')) {
    $containerBuilder->addDefinitions(__DIR__ . '/dependencies.php');
}

$container = $containerBuilder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Chargement des routes
if (file_exists(__DIR__ . '/routes.php')) {
    $routes = require __DIR__ . '/routes.php';
    if (is_callable($routes)) {
        $routes($app);
    }
}

return $app;