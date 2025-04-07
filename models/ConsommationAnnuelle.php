<?php
require_once 'connexion.php';
require_once __DIR__ . '/../models/ConsommationAnnuelle.php';

class ConsommationAnnuelleDAO {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Save or update annual consumption record
    public function enregistrer(ConsommationAnnuelle $consommationAnnuelle) {
        try {
            // Check if a record already exists for this client and year
            $stmt = $this->db->prepare("
                SELECT consommation_annuelle_id FROM consommations_annuelles 
                WHERE client_id = :client_id AND annee = :annee
            ");
            $stmt->execute([
                'client_id' => $consommationAnnuelle->getClientId(),
                'annee' => $consommationAnnuelle->getAnnee()
            ]);
            
            $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingRecord) {
                // Update existing record
                $stmt = $this->db->prepare("
                    UPDATE consommations_annuelles 
                    SET consommation_attendue = :consommation_attendue, 
                        consommation_reelle = :consommation_reelle, 
                        
                        statut = :statut,
                        notes = :notes
                    WHERE consommation_annuelle_id = :consommation_annuelle_id
                ");
                
                $stmt->execute([
                    'consommation_attendue' => $consommationAnnuelle->getConsommationAttendue(),
                    'consommation_reelle' => $consommationAnnuelle->getConsommationReelle(),
                    
                    'statut' => $consommationAnnuelle->getStatut(),
                    'notes' => $consommationAnnuelle->getNotes(),
                    'consommation_annuelle_id' => $existingRecord['consommation_annuelle_id']
                ]);
                
                $consommationAnnuelle->setId($existingRecord['consommation_annuelle_id']);
                return $consommationAnnuelle;
            } else {
                // Insert new record
                $stmt = $this->db->prepare("
                    INSERT INTO consommations_annuelles 
                    (client_id, annee, consommation_attendue, consommation_reelle, statut, notes, date_creation)
                    VALUES 
                    (:client_id, :annee, :consommation_attendue, :consommation_reelle, :statut, :notes, NOW())
                ");
                
                $stmt->execute([
                    'client_id' => $consommationAnnuelle->getClientId(),
                    'annee' => $consommationAnnuelle->getAnnee(),
                    'consommation_attendue' => $consommationAnnuelle->getConsommationAttendue(),
                    'consommation_reelle' => $consommationAnnuelle->getConsommationReelle(),
                    
                    'statut' => $consommationAnnuelle->getStatut(),
                    'notes' => $consommationAnnuelle->getNotes()
                ]);
                
                $consommationAnnuelle->setId($this->db->lastInsertId());
                return $consommationAnnuelle;
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'enregistrement de la consommation annuelle: " . $e->getMessage());
        }
    }
    
    // Get annual consumption by client ID and year
    public function getParClientEtAnnee($clientId, $annee) {
        $stmt = $this->db->prepare("
            SELECT * FROM consommations_annuelles 
            WHERE client_id = :client_id AND annee = :annee
        ");
        $stmt->execute(['client_id' => $clientId, 'annee' => $annee]);
        
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$record) return null;
        
        $consommation = new ConsommationAnnuelle(
            $record['client_id'],
            $record['annee'],
            $record['consommation_attendue'],
            $record['consommation_reelle']
        );
        
        $consommation->setId($record['consommation_annuelle_id']);
        $consommation->setStatut($record['statut']);
        $consommation->setNotes($record['notes']);
        
        return $consommation;
    }
    
    // Get all annual consumptions with discrepancies
    public function getTousAvecEcarts() {
        $stmt = $this->db->prepare("
            SELECT * FROM consommations_annuelles 
            WHERE ABS(ecart) > 0
            ORDER BY date_creation DESC
        ");
        $stmt->execute();
        
        $consommations = [];
        while ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $consommation = new ConsommationAnnuelle(
                $record['client_id'],
                $record['annee'],
                $record['consommation_attendue'],
                $record['consommation_reelle']
            );
            
            $consommation->setId($record['consommation_annuelle_id']);
            $consommation->setStatut($record['statut']);
            $consommation->setNotes($record['notes']);
            
            $consommations[] = $consommation;
        }
        
        return $consommations;
    }
    
    // Update status of annual consumption
    public function mettreAJourStatut($id, $statut, $notes = null) {
        $stmt = $this->db->prepare("
            UPDATE consommations_annuelles 
            SET statut = :statut, notes = :notes
            WHERE consommation_annuelle_id = :consommation_annuelle_id
        ");
        
        return $stmt->execute([
            'statut' => $statut,
            'notes' => $notes,
            'consommation_annuelle_id' => $id
        ]);
    }
}