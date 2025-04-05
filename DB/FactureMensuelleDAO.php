<?php
require_once __DIR__ . '/connexion.php';
require_once __DIR__ . '/../models/FactureMensuelle.php';

class FactureMensuelleDAO {
    private PDO $db;

    public function __construct() {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    // this returns all factures 
    public function getAllFacturesMensuelles() {
            $stmt = $this->db->prepare("SELECT * FROM factures_mensuelle");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetched as an array
            return $result;
    }

    // this returns all factures mensuelles issued for a client
    public function getFacturesMensuellesByClientId($clientId) {
            $stmt = $this->db->prepare("SELECT * FROM factures_mensuelle WHERE client_id = :client_id");
            $stmt->execute(['client_id' => $clientId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
    }

    public function getFactureById($id) {
        try {
            $query = "SELECT * FROM factures_mensuelle WHERE facture_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("FactureDAO::getFactureById Error: " . $e->getMessage());
            return null;
        }
    }

    public function getLastFactureByClientId(int $clientId): ?array {
        try {
            $query = "SELECT * FROM factures_mensuelle 
                     WHERE client_id = :clientId 
                     ORDER BY date_emission DESC 
                     LIMIT 1";
            // ORDER BY cm.created_at DESC
            $stmt = $this->db->prepare($query);
            $stmt->execute(['clientId' => $clientId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("FactureDAO::getLastFactureByClientId Error: " . $e->getMessage());
            return null;
        }
    }

    public function submitNewFactureMensuelle(FactureMensuelle $factureMensuelle) {

        $stmt = $this->db->prepare("
        INSERT INTO factures_mensuelle (
            client_id, 
            consommation_id,
            montant,
            client_name,
            consommation,
            date_emission
        ) VALUES (
            :client_id,
            :consommation_id,
            :montant,
            :client_name,
            :consommation,
            NOW())"
    );
        
        $stmt->execute([
            'client_id' => $factureMensuelle->getClientId(),
            'consommation_id' => $factureMensuelle->getConsommationId(),
            'montant' => $factureMensuelle->getMontant(),
            'client_name' => $factureMensuelle->getClientName(),
            'consommation' => $factureMensuelle->getConsommation()
        ]);

        return $this->db->lastInsertId();

    }
}