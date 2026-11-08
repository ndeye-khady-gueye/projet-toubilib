<?php

use function DI\get;
use function DI\create;

use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\infrastructure\repositories\PgPraticienRepository;
use toubilib\application\ports\api\ServicePraticienInterface;
use toubilib\application\services\ServicePraticien;

use toubilib\application\ports\spi\PatientRepositoryInterface;
use toubilib\infrastructure\repositories\PgPatientRepository;

use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\infrastructure\repositories\PgRendezVousRepository;

use toubilib\application\ports\api\ServiceRendezVousInterface;
use toubilib\application\services\ServiceRendezVous;

return [
    // --- Alias global pour PDO ---
    \PDO::class => get('toubilib.pdo'),

    // --- Repositories (SPI) ---
    PraticienRepositoryInterface::class => create(PgPraticienRepository::class)
        ->constructor(get('toubilib.pdo')),

    PatientRepositoryInterface::class => create(PgPatientRepository::class)
        ->constructor(get('toubilib.pdo')),

    RendezVousRepositoryInterface::class => create(PgRendezVousRepository::class)
        ->constructor(get('toubilib.pdo')),

    // --- Services Application (API) ---
    // Fonctionnalité 1 : Praticiens
    ServicePraticienInterface::class => create(ServicePraticien::class)
        ->constructor(get(PraticienRepositoryInterface::class)),

    // Fonctionnalités 5 & 6 : Rendez-vous (nécessite Praticien + Patient + RendezVous Repositories)
    ServiceRendezVousInterface::class => create(ServiceRendezVous::class)
        ->constructor(
            get(PraticienRepositoryInterface::class),
            get(PatientRepositoryInterface::class),
            get(RendezVousRepositoryInterface::class)
        ),
];