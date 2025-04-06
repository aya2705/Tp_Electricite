<?php

class notification{
    private $reclamation_id;
    private $contenu;
    private $statut;
    private $date_reponse;

    public function __construct($reclamation_id, $contenu, $statut, $date_reponse = null) {
        $this->reclamation_id = $reclamation_id;
        $this->contenu = $contenu;
        $this->statut = $statut;
        $this->date_reponse = $date_reponse;
    }

    public function getReclamationId() { return $this->reclamation_id; }
    public function getContenu() { return $this->contenu; }
    public function getStatut() { return $this->statut; }
    public function getDateReponse() { return $this->date_reponse; }

    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setDateReponse($date_reponse) { $this->date_reponse = $date_reponse; }
}