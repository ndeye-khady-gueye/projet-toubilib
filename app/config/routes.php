<?php

use Slim\App;
use toubilib\adapters\http\actions\GetPraticiensAction;
use toubilib\adapters\http\actions\GetPraticienByIdAction;
use toubilib\adapters\http\actions\CreateRendezVousAction;
use toubilib\adapters\http\actions\GetRendezVousAction; // <-- Utilise la bonne classe Action
use toubilib\adapters\http\actions\CancelRendezVousAction;

return function (App $app) {

    // ==========================================
    // MODULE PRATICIENS
    // ==========================================

    // Lister / Filtrer les praticiens
    $app->get('/praticiens', GetPraticiensAction::class)
        ->setName('get_praticiens');

    // Consulter le profil détaillé d'un praticien
    $app->get('/praticiens/{id}', GetPraticienByIdAction::class)
        ->setName('get_praticien_by_id');


    // ==========================================
    // MODULE RENDEZ-VOUS (RESTful TD1.4)
    // ==========================================

    // Planifier un nouveau rendez-vous
    $app->post('/rdvs', CreateRendezVousAction::class)
        ->setName('create_rdv');
    $app->post('/rendezvous', CreateRendezVousAction::class); // Alias de compatibilité

    // Consulter les détails d'un rendez-vous (Demandé dans l'Exercice 2 du TD : /rdvs/{id})
    $app->get('/rdvs/{id}', GetRendezVousAction::class)
        ->setName('get_rdv_by_id');
    $app->get('/rendezvous/{id}', GetRendezVousAction::class); // Alias de compatibilité

    // Annuler un rendez-vous (Demandé dans l'Exercice 1 du TD : /rdvs/{id}/annuler)
    $app->patch('/rdvs/{id}/annuler', CancelRendezVousAction::class)
        ->setName('cancel_rdv');
    $app->patch('/rendezvous/{id}/cancel', CancelRendezVousAction::class); // Alias de compatibilité

};