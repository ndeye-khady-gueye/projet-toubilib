<?php

use toubilib\adapters\http\actions\CancelRendezVousAction;
use toubilib\adapters\http\actions\GetPraticiensAction;
use toubilib\application\ports\api\ServiceRendezVousInterface;
use toubilib\application\ports\api\ServicePraticienInterface;

return [
    CancelRendezVousAction::class => \DI\create(CancelRendezVousAction::class)
        ->constructor(\DI\get(ServiceRendezVousInterface::class)),

    GetPraticiensAction::class => \DI\create(GetPraticiensAction::class)
        ->constructor(\DI\get(ServicePraticienInterface::class)),
];