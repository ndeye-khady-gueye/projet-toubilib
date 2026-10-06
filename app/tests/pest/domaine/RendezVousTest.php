<?php

use toubilib\domain\entities\RendezVous;
use toubilib\domain\exceptions\InvalidRendezVousStateException;

test('un rendez-vous valide dans le futur peut être annulé', function () {
    $rdv = new RendezVous('rdv-1', 'PLANIFIE', new DateTimeImmutable('+2 days'));
    
    $rdv->annuler('Empêchement personnel');

    expect($rdv->getEtat())->toBe('ANNULE');
    expect($rdv->getRaisonAnnulation())->toBe('Empêchement personnel');
});

test('impossible d\'annuler un rendez-vous dont la date est passée', function () {
    $rdv = new RendezVous('rdv-2', 'PLANIFIE', new DateTimeImmutable('-1 day'));

    $rdv->annuler('Raison tardive');
})->throws(InvalidRendezVousStateException::class, 'Impossible d\'annuler un rendez-vous dont la date est passée.');

test('impossible d\'annuler un rendez-vous déjà annulé', function () {
    $rdv = new RendezVous('rdv-3', 'ANNULE', new DateTimeImmutable('+2 days'));

    $rdv->annuler('Autre raison');
})->throws(InvalidRendezVousStateException::class, 'Le rendez-vous est déjà annulé.');

test('impossible d\'annuler un rendez-vous déjà honoré', function () {
    $rdv = new RendezVous('rdv-4', 'HONORE', new DateTimeImmutable('+2 days'));

    $rdv->annuler('Raison');
})->throws(InvalidRendezVousStateException::class, 'Impossible d\'annuler un rendez-vous déjà honoré.');

test('l\'annulation échoue si la raison est vide', function () {
    $rdv = new RendezVous('rdv-5', 'PLANIFIE', new DateTimeImmutable('+2 days'));

    $rdv->annuler('   ');
})->throws(InvalidArgumentException::class);