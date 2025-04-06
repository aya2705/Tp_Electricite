<?php

class FactureMensuelle {
    private $factureId;
    private $clientId;
    private $consommationId;
    private $clientName;
    private $consommationKw;
    private $montant;

    public function __construct(int $clientId, $clientName, ConsommationMensuelle $consommation, $consommationKw, $montant) {
        $this->clientId = $clientId;
        $this->clientName = $clientName;
        $this->consommationId = $consommation->getId();
        $this->consommationKw = $consommationKw;
        $this->montant = $montant;
    }

    // Getters
    public function getFactureId() { return $this->factureId; }
    public function getClientId() { return $this->clientId; }
    public function getClientName() { return $this->clientName; }
    public function getConsommationKw() { return $this->consommationKw; }
    public function getMontant() { return $this->montant; }
    public function getConsommationId() { return $this->consommationId; }
    
    // Setters
    public function setFactureId($factureId) { $this->factureId = $factureId; }
    public function setClientId($clientId) { $this->clientId = $clientId; }
    public function setClientName($clientName) { $this->clientName = $clientName; }
    public function setConsommation($consommationKw) { $this->consommationKw = $consommationKw; }
    public function setMontant($montant) { $this->montant = $montant; }
    public function setConsommationId($consommationId) { $this->consommationId = $consommationId; }
}