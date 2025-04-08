<?php
class ConsommationAnnuelle {
    private $id;
    private $clientId;
    private $annee;
    private $consommationAttendue;
    private $consommationReelle;
    private $statut;
    private $notes;
    private $clientName; // For display purposes
    
    public function __construct($clientId, $annee, $consommationAttendue, $consommationReelle = null) {
        $this->clientId = $clientId;
        $this->annee = $annee;
        $this->consommationAttendue = $consommationAttendue;
        $this->consommationReelle = $consommationReelle;
        $this->statut = 'en_attente';
        $this->notes = null;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getClientId() { return $this->clientId; }
    public function getAnnee() { return $this->annee; }
    public function getConsommationAttendue() { return $this->consommationAttendue; }
    public function getConsommationReelle() { return $this->consommationReelle; }
    public function getStatut() { return $this->statut; }
    public function getNotes() { return $this->notes; }
    public function getClientName() { return $this->clientName; }
    
    // Calculated getter for ecart
    public function getEcart() {
        if ($this->consommationReelle === null) {
            return null;
        }
        return $this->consommationAttendue - $this->consommationReelle;
    }
    
    // Setters
    public function setId($id) { $this->id = $id; }
    public function setClientId($clientId) { $this->clientId = $clientId; }
    public function setAnnee($annee) { $this->annee = $annee; }
    public function setConsommationAttendue($consommation) { $this->consommationAttendue = $consommation; }
    public function setConsommationReelle($consommation) { $this->consommationReelle = $consommation; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setNotes($notes) { $this->notes = $notes; }
    public function setClientName($name) { $this->clientName = $name; }
    
    // Check if discrepancy is significant (exceed threshold percentage)
    public function aEcartSignificatif($threshold = 5) {
        if ($this->consommationReelle === null || $this->consommationReelle == 0) {
            return false;
        }
        
        $ecartPct = abs($this->getEcart() / $this->consommationReelle * 100);
        return $ecartPct > $threshold;
    }
}