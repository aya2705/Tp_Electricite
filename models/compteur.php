<?php
class Compteur {
    private $compteur_id;
    private $client_id;
    private $numero_serie;

    public function __construct($compteur_id, $client_id, $numero_serie) {
        $this->compteur_id = $compteur_id;
        $this->client_id = $client_id;
        $this->numero_serie = $numero_serie;
    }

    public function getCompteurId() {
        return $this->compteur_id;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getNumeroSerie() {
        return $this->numero_serie;
    }
}
