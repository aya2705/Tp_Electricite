<?php
require_once '../../models/consommationMensuelle.php';
require_once 'connexion.php';
class ConsommationDAO
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // get lastly submitted consumption (Working well)
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
        $consumption->setIsAbnormal($row['est_anormale']);
        return $consumption;
    }

    // 
    public function saveConsumption($clientId, $consommationMensuelle){
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

        /*/ Get active period
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

        $stmt = $this->db->prepare("
             INSERT INTO consommations_mensuelles 
             (client_id, compteur_id, kw, image_path, created_at, est_anormale) 
             VALUES 
             (:client_id, :compteur_id, :kw, :image_path, NOW(), :est_anormale )");
        $stmt->execute([
            'client_id' => $clientId,
            'compteur_id' => $compteur['compteur_id'],
            'kw' => $consommationMensuelle->getKw(),
            'image_path' => $consommationMensuelle->getImagePath(),
            'est_anormale' => $consommationMensuelle->getIsAbnormal()
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
    }
 
    // get all consumptions wil isAbnormal set to true 
    public function getAllAbnormalMonthlyConsumptions(){
        return array(
            0 => new ConsommationMensuelle(4500, '/path', 'test', true),
            1 => new ConsommationMensuelle(4500, '/path', 'test', true),
        );
    }
    // corrects an abnormal consumption by setting isAbnormal to false and correcting the consumption in kw
    // returns the corrected consumption
    public function correctConsumption($consumptionId, $correctedKW, $isAbnormal) {}
}
