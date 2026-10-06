
<?php

use Slim\App;
use toubilib\adapters\http\actions\CancelRendezVousAction;

return function (App $app) {
    $app->patch('/rendezvous/{id}/annulation', CancelRendezVousAction::class);
};