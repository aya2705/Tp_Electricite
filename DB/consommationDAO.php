<?php
require_once __DIR__ . '/../models/consommationMensuelle.php';
require_once 'connexion.php';
class ConsommationDAO
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // get lastly submitted consumption (Working well)
    /* 
    public function getLastSubmittedConsumption($clientId){
        $stmt = $this->db->prepare("
                SELECT consommation_id, kw, image_path, created_at, est_anormale
                FROM consommations_mensuelles
                WHERE client_id = :client_id
                ORDER BY created_at DESC
                LIMIT 1
            ");
        $stmt->execute(['client_id' => $clientId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $consumption = new ConsommationMensuelle(
            $row['kw'],
            $row['image_path'],
            $row['created_at']
        );
        $consumption->setId($row['consommation_id']);
        return $consumption;
    } 
    */
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

/**
 * Calculate total consumption for a client in a specific year
 * 
 * @param int $clientId The client ID
 * @param int $annee The year
 * @return float The total consumption in kWh
 */
public function calculerConsommationTotaleAnnuelle($clientId, $annee) 
{
    try {
        $stmt = $this->db->prepare("
            SELECT SUM(kw) as total_consommation
            FROM consommations_mensuelles
            WHERE client_id = :client_id 
            AND YEAR(created_at) = :annee
        ");
        
        $stmt->execute([
            'client_id' => $clientId,
            'annee' => $annee
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_consommation'] ? floatval($result['total_consommation']) : 0;
    } catch (PDOException $e) {
        error_log("Error calculating annual consumption: " . $e->getMessage());
        return 0;
    }
}
    /* 
    public function saveConsumption($clientId, $consommationMensuelle)
    {
        echo "reached the DAO method for saving new conumption";
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
        echo "retrieved the compteur for the client";
        $stmt = $this->db->prepare("
             INSERT INTO consommations_mensuelles 
             (client_id, compteur_id, kw, image_path, created_at) 
             VALUES 
             (:client_id, :compteur_id, :kw, :image_path, NOW() )");
        $stmt->execute([
            'client_id' => $clientId,
            'compteur_id' => $compteur['compteur_id'],
            'kw' => $consommationMensuelle->getKw(),
            'image_path' => $consommationMensuelle->getImagePath()
        ]);

        echo "persisted the consumption now returning its id so that we can create the facture";
        $consumptionId = $this->db->lastInsertId();

        $consumption = new ConsommationMensuelle(
            $consommationMensuelle->getKw(),
            $consommationMensuelle->getImagePath(),
            date('Y-m-d H:i:s')
        );

        $consumption->setId($consumptionId);

        return $consumption;
    } */
    /* Get active period
        $stmtPeriode = $this->db->prepare("
            SELECT periode_id 
            FROM periodes_saisie 
            WHERE est_active = TRUE 
            AND NOW() BETWEEN date_debut AND date_fin 
            LIMIT 1
        ");
        $stmtPeriode->execute();
        $periode = $stmtPeriode->fetch();
            
        if (!$periode) {
            throw new Exception("No active period found for consumption submission");
        }
        */


    // Insert the new consumption
    /*$stmt = $this->db->prepare("
             INSERT INTO consommations_mensuelles 
             (client_id, compteur_id, periode_id, kw, valeur_precedente, image_path, created_at) 
             VALUES 
             (:client_id, :compteur_id, :periode_id, :kw, :valeur_precedente, :image_path, NOW())
         "); */



    // get all consumptions wil isAbnormal set to true 
   /* public function getAllAbnormalMonthlyConsumptions()
    {
        return array(
            0 => new ConsommationMensuelle(4500, '/path', 'test', true),
            1 => new ConsommationMensuelle(4500, '/path', 'test', true),
        );
    } */
    
    // corrects an abnormal consumption by setting isAbnormal to false and correcting the consumption in kw
    // returns the corrected consumption
    public function correctConsumption($consumptionId, $correctedKW, $isAbnormal) {}
}
