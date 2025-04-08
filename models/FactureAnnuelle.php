<?php
class FactureAnnuelle {
    private $factureId;
    private $clientId;
    private $consommationAnnuelleId;
    private $annee;
    private $montantTotal;
    private $consommationTotale;
    private $dateEmission;
    private $statut;
    private $clientName; 
    private $type; // 'credit' or 'debit'
    
    public function __construct($clientId, $consommationAnnuelleId, $annee, $montantTotal, $consommationTotale, $type = 'debit') {
        $this->clientId = $clientId;
        $this->consommationAnnuelleId = $consommationAnnuelleId;
        $this->annee = $annee;
        $this->montantTotal = $montantTotal;
        $this->consommationTotale = $consommationTotale;
        $this->statut = 'emise';
        $this->dateEmission = date('Y-m-d H:i:s');
        $this->type = $type; // Set default to debit
    }
    
    // Getters
    public function getFactureId() { return $this->factureId; }
    public function getClientId() { return $this->clientId; }
    public function getConsommationAnnuelleId() { return $this->consommationAnnuelleId; }
    public function getAnnee() { return $this->annee; }
    public function getMontantTotal() { return $this->montantTotal; }
    public function getConsommationTotale() { return $this->consommationTotale; }
    public function getDateEmission() { return $this->dateEmission; }
    public function getStatut() { return $this->statut; }
    public function getClientName() { return $this->clientName; }
    public function getType() { return $this->type; }
    
    // Setters
    public function setFactureId($id) { $this->factureId = $id; }
    public function setClientId($id) { $this->clientId = $id; }
    public function setConsommationAnnuelleId($id) { $this->consommationAnnuelleId = $id; }
    public function setAnnee($annee) { $this->annee = $annee; }
    public function setMontantTotal($montant) { $this->montantTotal = $montant; }
    public function setConsommationTotale($consommation) { $this->consommationTotale = $consommation; }
    public function setDateEmission($date) { $this->dateEmission = $date; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setClientName($name) { $this->clientName = $name; }
    public function setType($type) { $this->type = $type; }
}