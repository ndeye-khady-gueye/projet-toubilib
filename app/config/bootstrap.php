<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

// On remonte d'un cran depuis app/config/ pour atteindre app/vendor/autoload.php
require_once __DIR__ . '/../vendor/autoload.php';

// 1. Initialisation du conteneur PHP-DI
$containerBuilder = new ContainerBuilder();

// 2. Chargement des définitions (directement dans le même dossier)
$containerBuilder->addDefinitions(__DIR__ . '/settings.php');
$containerBuilder->addDefinitions(__DIR__ . '/services.php');
$containerBuilder->addDefinitions(__DIR__ . '/api.php');

$container = $containerBuilder->build();

// 3. Liaison du conteneur à Slim
AppFactory::setContainer($container);
$app = AppFactory::create();

// 4. Middlewares de Slim
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// 5. Chargement dynamique des routes
$routesFile = __DIR__ . '/routes.php';
if (file_exists($routesFile)) {
    $routes = require $routesFile;
    if (is_callable($routes)) {
        $routes($app);
    }
}

return $app;