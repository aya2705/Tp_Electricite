<?php
require_once __DIR__ . '/../models/Tarif.php';

class TarifDAO {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }


    public function getById($tarif_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tarifs WHERE tarif_id = ?");
        $stmt->execute([$tarif_id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new Tarif($data);
    }

  
    public function getCurrentTarif() {
        $currentDate = date('Y-m-d');
        $stmt = $this->conn->prepare("
            SELECT * FROM tarifs 
            WHERE date_debut <= ? AND (date_fin IS NULL OR date_fin >= ?)
            ORDER BY date_debut DESC
            LIMIT 1
        ");
        $stmt->execute([$currentDate, $currentDate]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new Tarif($data);
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM tarifs ORDER BY date_debut DESC");
        $stmt->execute();
        
        $tarifs = [];
        while ($data = $stmt->fetch()) {
            $tarifs[] = new Tarif($data);
        }
        
        return $tarifs;
    }

   
    public function create(Tarif $tarif) {
        $stmt = $this->conn->prepare("
            INSERT INTO tarifs 
            (date_debut, date_fin, tranche1_max, tranche1_prix, tranche2_max, tranche2_prix, tranche3_prix, tva)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $tarif->getDateDebut(),
            $tarif->getDateFin(),
            $tarif->getTranche1Max(),
            $tarif->getTranche1Prix(),
            $tarif->getTranche2Max(),
            $tarif->getTranche2Prix(),
            $tarif->getTranche3Prix(),
            $tarif->getTva()
        ]);
        
        $tarif->setTarifId($this->conn->lastInsertId());
        return $tarif;
    }
}