<?php
class Tarif {
    private $tarif_id;
    private $date_debut;
    private $date_fin;
    private $tranche1_max;
    private $tranche1_prix;
    private $tranche2_max;
    private $tranche2_prix;
    private $tranche3_prix;
    private $tva;

    public function __construct($data) {
        $this->tarif_id = $data['tarif_id'];
        $this->date_debut = $data['date_debut'];
        $this->date_fin = $data['date_fin'] ?? null;
        $this->tranche1_max = $data['tranche1_max'];
        $this->tranche1_prix = $data['tranche1_prix'];
        $this->tranche2_max = $data['tranche2_max'] ?? null;
        $this->tranche2_prix = $data['tranche2_prix'];
        $this->tranche3_prix = $data['tranche3_prix'];
        $this->tva = $data['tva'];
    }


    public function getTarifId() { return $this->tarif_id; }
    public function getDateDebut() { return $this->date_debut; }
    public function getDateFin() { return $this->date_fin; }
    public function getTranche1Max() { return $this->tranche1_max; }
    public function getTranche1Prix() { return $this->tranche1_prix; }
    public function getTranche2Max() { return $this->tranche2_max; }
    public function getTranche2Prix() { return $this->tranche2_prix; }
    public function getTranche3Prix() { return $this->tranche3_prix; }
    public function getTva() { return $this->tva; }
    public function getPenaliteRetard() { return 0.10; } 


    public function setTarifId($id) { $this->tarif_id = $id; }
    public function setDateFin($date) { $this->date_fin = $date; }
}