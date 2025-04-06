<?php
require_once __DIR__ . '/../models/consommationMensuelle.php';
require_once 'connexion.php';

if (!class_exists('ConsommationDAO')) {
    class ConsommationDAO
    {
        private $db;

        public function __construct()
        {
            $this->db = Database::getInstance()->getConnection();
        }

        // Get the last normal consumption for a specific client
        public function getLastNormalConsumption($clientId)
        {
            $stmt = $this->db->prepare("
            SELECT cm.consommation_id, cm.kw, cm.image_path, cm.created_at
            FROM consommations_mensuelles cm
            LEFT JOIN anomalies_consommation ac ON cm.consommation_id = ac.consommation_id
            WHERE cm.client_id = :client_id
            AND ac.anomalie_id IS NULL
            ORDER BY cm.created_at DESC
            LIMIT 1
        ");
            $stmt->execute(['client_id' => $clientId]);
            $row = $stmt->fetch();
            if (!$row) {
                return null;
            }
            $consumption = new ConsommationMensuelle($row['kw'], $row['image_path'], $row['created_at']);
            $consumption->setId($row['consommation_id']);
            return $consumption;
        }

        public function getConsumptionById($consumptionId)
        {
            $stmt = $this->db->prepare("
        SELECT * 
        FROM consommations_mensuelles 
        WHERE consommation_id = :consommation_id 
    ");

            $stmt->execute(['consommation_id' => $consumptionId]);
            $row = $stmt->fetch();
            if (!$row) {
                return null;
            }

            $consumption = new ConsommationMensuelle($row['kw'], $row['image_path'], $row['created_at']);
            $consumption->setId($row['consommation_id']);
            return $consumption;
        }
        // Save a new consumption and return both the consumption object and compteur_id
        public function saveConsumption($clientId, $consommationMensuelle)
        {
            // Get the compteur_id for this client
            $stmtCompteur = $this->db->prepare("
        SELECT compteur_id 
        FROM compteurs 
        WHERE client_id = :client_id 
        LIMIT 1
    ");
            $stmtCompteur->execute(['client_id' => $clientId]);
            $compteur = $stmtCompteur->fetch();
            if (!$compteur) {
                throw new Exception("No compteur found for this client");
            }

            // Insert the new consumption
            $stmt = $this->db->prepare("
        INSERT INTO consommations_mensuelles 
        (client_id, compteur_id, kw, image_path, created_at) 
        VALUES 
        (:client_id, :compteur_id, :kw, :image_path, NOW())
    ");
            $stmt->execute([
                'client_id' => $clientId,
                'compteur_id' => $compteur['compteur_id'],
                'kw' => $consommationMensuelle->getKw(),
                'image_path' => $consommationMensuelle->getImagePath()
            ]);
            $consumptionId = $this->db->lastInsertId();

            // Fetch the inserted consumption to get created_at
            $stmtFetch = $this->db->prepare("
        SELECT consommation_id, kw, image_path, created_at, compteur_id
        FROM consommations_mensuelles
        WHERE consommation_id = :consommation_id
    ");
            $stmtFetch->execute(['consommation_id' => $consumptionId]);
            $row = $stmtFetch->fetch();

            if ($row) {
                $consumption = new ConsommationMensuelle(
                    $row['kw'],
                    $row['image_path'],
                    $row['created_at']
                );
                $consumption->setId($row['consommation_id']);
                return [
                    'consumption' => $consumption,
                    'compteur_id' => $row['compteur_id']
                ];
            } else {
                throw new Exception("Failed to fetch inserted consumption");
            }
        }

        // Create an anomaly with additional details
        public function createAnomaly($consommationId, $previousConsommationId, $clientName, $compteurId, $entryDate, $previousValue, $enteredValue, $difference, $imagePath)
        {
            $stmt = $this->db->prepare("
        INSERT INTO anomalies_consommation 
        (consommation_id, previous_consommation_id, client_name, compteur_id, entry_date, previous_value, entered_value, difference, status, created_at, image_path) 
        VALUES 
        (:consommation_id, :previous_consommation_id, :client_name, :compteur_id, :entry_date, :previous_value, :entered_value, :difference, 'en_attente', NOW(), :image_path)
    ");
            $stmt->execute([
                'consommation_id' => $consommationId,
                'previous_consommation_id' => $previousConsommationId,
                'client_name' => $clientName,
                'compteur_id' => $compteurId,
                'entry_date' => $entryDate,
                'previous_value' => $previousValue,
                'entered_value' => $enteredValue,
                'difference' => $difference,
                'image_path' => $imagePath
            ]);
        }

        // Get client name by client ID
        public function getClientName($clientId)
        {
            $stmt = $this->db->prepare("
        SELECT full_name 
        FROM clients 
        WHERE client_id = :client_id
    ");
            $stmt->execute(['client_id' => $clientId]);
            $row = $stmt->fetch();
            return $row ? $row['full_name'] : null;
        }

        // Get all anomalies (updated to reflect new columns)
        public function getAllAnomalies()
        {
            $stmt = $this->db->prepare("
        SELECT 
            a.anomalie_id,
            a.client_name,
            a.compteur_id,
            a.entry_date,
            a.previous_value,
            a.entered_value,
            a.difference,
            a.status,
            a.image_path
        FROM anomalies_consommation a
        WHERE a.status = 'en_attente'
    ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getConsumptionByAnomalyId($anomalieId)
        {
            $stmt = $this->db->prepare("
            SELECT cm.* 
            FROM consommations_mensuelles cm
            INNER JOIN anomalies_consommation ac ON cm.consommation_id = ac.consommation_id
            WHERE ac.anomalie_id = :anomalieId
        ");

            $stmt->execute(['anomalieId' => $anomalieId]);
            $row = $stmt->fetch();

            if (!$row) {
                return null;
            }

            $consumption = new ConsommationMensuelle($row['kw'], $row['image_path'], $row['created_at']);
            $consumption->setId($row['consommation_id']);
            return $consumption;
        }

        public function getAnomalyDetails($anomalyId) {
            $stmt = $this->db->prepare("
                SELECT a.*, c.client_id 
                FROM anomalies_consommation a
                JOIN consommations_mensuelles c ON a.consommation_id = c.consommation_id
                WHERE a.anomalie_id = :anomalyId
            ");
            $stmt->execute(['anomalyId' => $anomalyId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // corrects an abnormal consumption by setting isAbnormal to false and correcting the consumption in kw
        // returns the corrected consumption
        public function correctConsumptionAndDeleteAnomaly($anomalyId, $correctedKW)
        {
            try {
                $this->db->beginTransaction();

                // First, get the anomaly details to find the consumption
                $stmt = $this->db->prepare("
                SELECT ac.*, cm.client_id 
                FROM anomalies_consommation ac
                INNER JOIN consommations_mensuelles cm ON ac.consommation_id = cm.consommation_id
                WHERE ac.anomalie_id = :anomalyId
            ");
                $stmt->execute(['anomalyId' => $anomalyId]);
                $anomaly = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$anomaly) {
                    throw new Exception("Anomaly not found");
                }

                // Update the consumption with corrected value
                $stmtUpdate = $this->db->prepare("
                UPDATE consommations_mensuelles 
                SET kw = :correctedKW 
                WHERE consommation_id = :consommationId
            ");
                $stmtUpdate->execute([
                    'correctedKW' => $correctedKW,
                    'consommationId' => $anomaly['consommation_id']
                ]);

                // Delete the anomaly
                $stmtDelete = $this->db->prepare("
                DELETE FROM anomalies_consommation 
                WHERE anomalie_id = :anomalyId
            ");
                $stmtDelete->execute(['anomalyId' => $anomalyId]);

                // Get client name
                $clientName = $this->getClientName($anomaly['client_id']);

                // Create ConsommationMensuelle object with corrected value
                $persistedConsumption = new ConsommationMensuelle(
                    $correctedKW,
                    $anomaly['image_path'],
                    date('Y-m-d H:i:s')
                );
                $persistedConsumption->setId($anomaly['consommation_id']);

                $this->db->commit();

                return [
                    'clientId' => $anomaly['client_id'],
                    'clientName' => $clientName,
                    'persistedConsumption' => $persistedConsumption
                ];

            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to correct consumption: " . $e->getMessage());
            }
        }
    }
}