<?php

class FactureMensuelle {
    private $factureId;
    private $clientId;
    private $consommationId;
    private $clientName;
    private $consommation;
    private $montant;

    public function __construct(int $clientId, $clientName, ConsommationMensuelle $consommation, $montant) {
        $this->clientId = $clientId;
        $this->clientName = $clientName;
        $this->consommationId = $consommation->getId();
        $this->consommation = $consommation->getKw();
        $this->montant = $montant;
    }

    // Getters
    public function getFactureId() { return $this->factureId; }
    public function getClientId() { return $this->clientId; }
    public function getClientName() { return $this->clientName; }
    public function getConsommation() { return $this->consommation; }
    public function getMontant() { return $this->montant; }
    public function getConsommationId() { return $this->consommationId; }

    // Setters
    public function setFactureId($factureId) { $this->factureId = $factureId; }
    public function setClientId($clientId) { $this->clientId = $clientId; }
    public function setClientName($clientName) { $this->clientName = $clientName; }
    public function setConsommation($consommation) { $this->consommation = $consommation; }
    public function setMontant($montant) { $this->montant = $montant; }
    public function setConsommationId($consommationId) { $this->consommationId = $consommationId; }
}