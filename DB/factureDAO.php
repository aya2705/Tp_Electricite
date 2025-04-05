<?php
require_once __DIR__ . '/connexion.php';

class FactureDAO {
    private PDO $db;

    public function __construct() {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public function getAllFactures(): array {
        try {
            // First verify the connection is active
            if (!$this->db) {
                throw new Exception("Database connection not established");
            }

            // Simple query first to test
            $query = "SELECT * FROM factures";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log the results for debugging
            error_log("Query results: " . print_r($result, true));
            
            return $result;

        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            throw new Exception("Failed to fetch factures: " . $e->getMessage());
        }
    }
    

    public function getClientFactures($clientId) {
        try {
            // Debug log
            error_log("Fetching factures for client ID: " . $clientId);

            $query = "SELECT * FROM factures WHERE client_id = :clientId";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':clientId', $clientId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetchAll();
            
            // Debug log
            error_log("Found " . count($result) . " factures");
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error in getClientFactures: " . $e->getMessage());
            return [];
        }
    }
    public function getFactureById($id) {
        try {
            $query = "SELECT * FROM factures WHERE facture_id = :id";
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
            $query = "SELECT * FROM factures 
                     WHERE client_id = :clientId 
                     ORDER BY date_emission DESC 
                     LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute(['clientId' => $clientId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("FactureDAO::getLastFactureByClientId Error: " . $e->getMessage());
            return null;
        }
    }
}