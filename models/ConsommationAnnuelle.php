<?php
class ConsommationAnnuelle {
    private $id;
    private $clientId;
    private $annee;
    private $consommationAttendue;  // Value from the uploaded file (sensor data)
    private $consommationReelle;    // Sum of monthly submissions
    private $ecart;                 // Difference between expected and actual
    private $statut;                // 'en_attente', 'verifie', 'corrige'
    private $notes;                 // Any notes about discrepancies
    private $dateCreation;

    public function __construct($clientId, $annee, $consommationAttendue, $consommationReelle = null) {
        $this->clientId = $clientId;
        $this->annee = $annee;
        $this->consommationAttendue = $consommationAttendue;
        $this->consommationReelle = $consommationReelle;
        $this->calculerEcart();
        $this->statut = 'en_attente';
        $this->dateCreation = new DateTime();
    }

    public function calculerEcart() {
        if ($this->consommationReelle !== null) {
            $this->ecart = $this->consommationAttendue - $this->consommationReelle;
        }
    }

    // Getters and setters
    public function getId() { return $this->id; }
    public function getClientId() { return $this->clientId; }
    public function getAnnee() { return $this->annee; }
    public function getConsommationAttendue() { return $this->consommationAttendue; }
    public function getConsommationReelle() { return $this->consommationReelle; }
    public function getEcart() { return $this->ecart; }
    public function getStatut() { return $this->statut; }
    public function getNotes() { return $this->notes; }
    public function getDateCreation() { return $this->dateCreation; }

    public function setId($id) { $this->id = $id; }
    public function setConsommationReelle($consommationReelle) { 
        $this->consommationReelle = $consommationReelle; 
        $this->calculerEcart();
    }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setNotes($notes) { $this->notes = $notes; }
    
    // Method to check if there's a significant discrepancy
    public function aEcartSignificatif($seuil = 5) {
        // Consider discrepancy significant if it's more than threshold %
        if ($this->consommationReelle == 0) return true;
        
        $ecartPourcentage = abs($this->ecart / $this->consommationReelle * 100);
        return $ecartPourcentage > $seuil;
    }
}