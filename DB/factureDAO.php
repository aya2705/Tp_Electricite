<?php
require_once __DIR__ . '/../models/Facture.php';

// DB/factureDAO.php
class FactureDAO {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create(Facture $facture) {
        $stmt = $this->conn->prepare("
            INSERT INTO factures 
            (client_id, periode, consommation, montant, statut_paiement, date_emission, tarif_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $facture->getClientId(),
            $facture->getPeriode(),
            $facture->getConsommation(),
            $facture->getMontant(),
            $facture->getStatutPaiement(),
            $facture->getDateEmission(),
            $facture->getTarifId()
        ]);
        
        $facture->setFactureId($this->conn->lastInsertId());
        return $facture;
    }

    public function update(Facture $facture) {
        $stmt = $this->conn->prepare("
            UPDATE factures SET 
            statut_paiement = ?, 
            date_paiement = ?, 
            penalites = ? 
            WHERE facture_id = ?
        ");
        
        return $stmt->execute([
            $facture->getStatutPaiement(),
            $facture->getDatePaiement(),
            $facture->getPenalites(),
            $facture->getFactureId()
        ]);
    }

    public function getById($facture_id) {
        $stmt = $this->conn->prepare("SELECT * FROM factures WHERE facture_id = ?");
        $stmt->execute([$facture_id]);
        $data = $stmt->fetch();
        
        return $data ? new Facture($data) : null;
    }

    public function getByClient($client_id, $limit = null) {
        $sql = "SELECT * FROM factures WHERE client_id = ? ORDER BY periode DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$client_id]);
        
        $factures = [];
        while ($data = $stmt->fetch()) {
            $factures[] = new Facture($data);
        }
        
        return $factures;
    }

    public function getFacturesImpayees() {
        $stmt = $this->conn->prepare("
            SELECT f.* 
            FROM factures f
            WHERE f.statut_paiement = 'impayée'
            AND f.date_emission < DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute();
        
        $factures = [];
        while ($data = $stmt->fetch()) {
            $factures[] = new Facture($data);
        }
        
        return $factures;
    }

    public function marquerCommePayee($facture_id, $date_paiement = null) {
        $date = $date_paiement ?: date('Y-m-d');
        $stmt = $this->conn->prepare("
            UPDATE factures 
            SET statut_paiement = 'payée', date_paiement = ?
            WHERE facture_id = ?
        ");
        
        return $stmt->execute([$date, $facture_id]);
    }
}
?>