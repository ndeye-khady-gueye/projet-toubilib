<?php

use function DI\autowire;

use toubilib\application\ports\api\ServiceRendezVousInterface;
use toubilib\application\ports\api\ServicePraticienInterface;

use toubilib\application\services\ServiceRendezVous;
use toubilib\application\services\ServicePraticien;

use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\application\ports\spi\PatientRepositoryInterface;

// Utiliser RendezVousRepository au lieu de PgRendezVousRepository
use toubilib\adapters\persistence\RendezVousRepository;
use toubilib\adapters\persistence\PgPraticienRepository;
use toubilib\adapters\persistence\PgPatientRepository;

use toubilib\adapters\http\actions\GetPraticiensAction;
use toubilib\adapters\http\actions\CreateRendezVousAction;
use toubilib\adapters\http\actions\CancelRendezVousAction;

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
            $ini['lib.username'] ?? 'toubilib',
            $ini['lib.password'] ?? 'toubilib',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    },

    // 2. Association des interfaces SPI aux implémentations de persistance (Repositories)
    RendezVousRepositoryInterface::class => autowire(RendezVousRepository::class),
    PraticienRepositoryInterface::class  => autowire(PgPraticienRepository::class),
    PatientRepositoryInterface::class    => autowire(PgPatientRepository::class),

    // 3. Association des interfaces API aux services applicatifs
    ServiceRendezVousInterface::class => autowire(ServiceRendezVous::class),
    ServicePraticienInterface::class  => autowire(ServicePraticien::class),

    // 4. Injections automatiques des Actions HTTP (Slim Controllers)
    GetPraticiensAction::class      => autowire(GetPraticiensAction::class),
    CreateRendezVousAction::class   => autowire(CreateRendezVousAction::class),
    CancelRendezVousAction::class   => autowire(CancelRendezVousAction::class),
];