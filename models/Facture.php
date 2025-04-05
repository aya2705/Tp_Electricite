<?php

class Facture {
    private $factureId;
    private $clientId;
    private $clientName; // For display purposes
    private $periode;
    private $consommation;
    private $montant;
    private $statutPaiement;
    private $dateEmission;
    private $tarifId;

    public function __construct($data = []) {
        $this->factureId = $data['facture_id'] ?? null;
        $this->clientId = $data['client_id'] ?? null;
        $this->clientName = $data['client_name'] ?? '';
        $this->periode = $data['periode'] ?? '';
        $this->consommation = $data['consommation'] ?? 0;
        $this->montant = $data['montant'] ?? 0.00;
        $this->statutPaiement = $data['statut_paiement'] ?? 'impayée';
        $this->dateEmission = $data['date_emission'] ?? '';
        $this->tarifId = $data['tarif_id'] ?? null;
    }

    // Getters
    public function getFactureId() { return $this->factureId; }
    public function getClientId() { return $this->clientId; }
    public function getClientName() { return $this->clientName; }
    public function getPeriode() { return $this->periode; }
    public function getConsommation() { return $this->consommation; }
    public function getMontant() { return $this->montant; }
    public function getStatutPaiement() { return $this->statutPaiement; }
    public function getDateEmission() { return $this->dateEmission; }
    public function getTarifId() { return $this->tarifId; }

    // Setters
    public function setFactureId($factureId) { $this->factureId = $factureId; }
    public function setClientId($clientId) { $this->clientId = $clientId; }
    public function setClientName($clientName) { $this->clientName = $clientName; }
    public function setPeriode($periode) { $this->periode = $periode; }
    public function setConsommation($consommation) { $this->consommation = $consommation; }
    public function setMontant($montant) { $this->montant = $montant; }
    public function setStatutPaiement($statutPaiement) { $this->statutPaiement = $statutPaiement; }
    public function setDateEmission($dateEmission) { $this->dateEmission = $dateEmission; }
    public function setTarifId($tarifId) { $this->tarifId = $tarifId; }
}