<?php
class Facture {
    private $facture_id;
    private $client_id;
    private $periode;
    private $consommation;
    private $montant;
    private $statut_paiement;
    private $date_emission;
    private $tarif_id;
    private $date_paiement;
    private $penalites;

    public function __construct($data) {
        $this->facture_id = $data['facture_id'] ?? null;
        $this->client_id = $data['client_id'];
        $this->periode = $data['periode'];
        $this->consommation = $data['consommation'];
        $this->montant = $data['montant'];
        $this->statut_paiement = $data['statut_paiement'] ?? 'impayée';
        $this->date_emission = $data['date_emission'] ?? date('Y-m-d');
        $this->tarif_id = $data['tarif_id'];
        $this->date_paiement = $data['date_paiement'] ?? null;
        $this->penalites = $data['penalites'] ?? 0;
    }

  
    public function getFactureId() { return $this->facture_id; }
    public function getClientId() { return $this->client_id; }
    public function getPeriode() { return $this->periode; }
    public function getConsommation() { return $this->consommation; }
    public function getMontant() { return $this->montant; }
    public function getStatutPaiement() { return $this->statut_paiement; }
    public function getDateEmission() { return $this->date_emission; }
    public function getTarifId() { return $this->tarif_id; }
    public function getDatePaiement() { return $this->date_paiement; }
    public function getPenalites() { return $this->penalites; }

 
    public function setStatutPaiement($statut) { $this->statut_paiement = $statut; }
    public function setDatePaiement($date) { $this->date_paiement = $date; }
    public function setPenalites($penalites) { $this->penalites = $penalites; }

  
    public function estEnRetard() {
        if ($this->statut_paiement === 'payée') {
            return false;
        }
        
        $dateLimite = date('Y-m-d', strtotime($this->date_emission . ' +30 days'));
        return date('Y-m-d') > $dateLimite;
    }

  
    public function calculerPenalites(Tarif $tarif) {
        if (!$this->estEnRetard()) {
            return 0;
        }
        
        $joursRetard = $this->getJoursRetard();
        $penalite = $this->montant * $tarif->getPenaliteRetard() * ($joursRetard / 30);
        return $penalite;
    }

    private function getJoursRetard() {
        $dateLimite = date('Y-m-d', strtotime($this->date_emission . ' +30 days'));
        $diff = date_diff(date_create($dateLimite), date_create());
        return $diff->days;
    }
}
?>
