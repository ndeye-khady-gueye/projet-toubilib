<?php

namespace toubilib\domain\entities;

class Patient
{
    private string $id;
    private string $nom;
    private string $prenom;

    public function __construct(string $id, string $nom, string $prenom)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
    }

    public function getId(): string { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
}