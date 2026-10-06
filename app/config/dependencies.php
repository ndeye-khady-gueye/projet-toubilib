<?php

use function DI\autowire;

use toubilib\application\ports\api\ServiceRendezVousInterface;
use toubilib\application\usecases\ServiceRendezVous;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
// C'est ici que j'ai corrigé "infrastructure" par "persistence" 
use toubilib\adapters\persistence\RendezVousRepository; 

return [
    // 1. Configuration de la connexion PDO à PostgreSQL
    PDO::class => function () {
        $ini = parse_ini_file(__DIR__ . '/.env.dist');
        
        $dsn = sprintf(
            "%s:host=%s;dbname=%s",
            $ini['lib.driver'] ?? 'pgsql',
            $ini['lib.host'] ?? 'toubilib.db',
            $ini['lib.database'] ?? 'toubilib'
        );

        return new PDO(
            $dsn,
            $ini['lib.username'] ?? 'user',
            $ini['lib.password'] ?? '1234',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    },

    // 2. Association de l'interface SPI à son implémentation PDO
    RendezVousRepositoryInterface::class => autowire(RendezVousRepository::class),

    // 3. Association de l'interface API au service métier
    ServiceRendezVousInterface::class => autowire(ServiceRendezVous::class),
];