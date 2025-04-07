<?php
require_once 'connexion.php';
require_once __DIR__ . '/../models/FactureAnnuelle.php';

class FactureAnnuelleDAO {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Create a new annual invoice
    public function creerFacture(FactureAnnuelle $factureAnnuelle) {
        try {
            // Check if invoice already exists for this client and year
            $stmt = $this->db->prepare("
                SELECT facture_annuelle_id FROM factures_annuelles 
                WHERE client_id = :client_id AND annee = :annee
            ");
            $stmt->execute([
                'client_id' => $factureAnnuelle->getClientId(),
                'annee' => $factureAnnuelle->getAnnee()
            ]);
            
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("Une facture annuelle existe déjà pour ce client et cette année");
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO factures_annuelles 
                (client_id, consommation_annuelle_id, annee, montant_total, consommation_totale, date_emission, statut) 
                VALUES 
                (:client_id, :consommation_annuelle_id, :annee, :montant_total, :consommation_totale, NOW(), :statut)
            ");
            
            $stmt->execute([
                'client_id' => $factureAnnuelle->getClientId(),
                'consommation_annuelle_id' => $factureAnnuelle->getConsommationAnnuelleId(),
                'annee' => $factureAnnuelle->getAnnee(),
                'montant_total' => $factureAnnuelle->getMontantTotal(),
                'consommation_totale' => $factureAnnuelle->getConsommationTotale(),
                'statut' => $factureAnnuelle->getStatut()
            ]);
            
            $factureAnnuelle->setFactureId($this->db->lastInsertId());
            return $factureAnnuelle;
            
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de la facture annuelle: " . $e->getMessage());
        }
    }
    
    // Get annual invoice by ID
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT fa.*, c.full_name 
            FROM factures_annuelles fa
            JOIN clients c ON fa.client_id = c.client_id
            WHERE fa.facture_annuelle_id = :id
        ");
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        
        $facture = new FactureAnnuelle(
            $row['client_id'],
            $row['consommation_annuelle_id'],
            $row['annee'],
            $row['montant_total'],
            $row['consommation_totale']
        );
        
        $facture->setFactureId($row['facture_annuelle_id']);
        $facture->setDateEmission($row['date_emission']);
        $facture->setStatut($row['statut']);
        $facture->setClientName($row['full_name']);
        
        return $facture;
    }
    
    // Get all annual invoices for a client
    public function getFacturesAnnuellesByClientId($clientId) {
        $stmt = $this->db->prepare("
            SELECT fa.*, c.full_name, ca.ecart, ca.consommation_attendue, ca.consommation_reelle
            FROM factures_annuelles fa
            JOIN clients c ON fa.client_id = c.client_id
            JOIN consommations_annuelles ca ON fa.consommation_annuelle_id = ca.consommation_annuelle_id
            WHERE fa.client_id = :client_id
            ORDER BY fa.annee DESC
        ");
        $stmt->execute(['client_id' => $clientId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get all annual invoices (for admin)
    public function getAllFacturesAnnuelles() {
        $stmt = $this->db->prepare("
            SELECT fa.*, c.full_name
            FROM factures_annuelles fa
            JOIN clients c ON fa.client_id = c.client_id
            ORDER BY fa.date_emission DESC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update invoice status
    public function updateStatus($id, $statut) {
        $stmt = $this->db->prepare("
            UPDATE factures_annuelles 
            SET statut = :statut
            WHERE facture_annuelle_id = :id
        ");
        
        return $stmt->execute([
            'statut' => $statut,
            'id' => $id
        ]);
    }

    /**
 * Check if an invoice already exists for a client and year
 * 
 * @param int $clientId Client ID
 * @param int $annee Year
 * @return array|null The invoice data if it exists, null otherwise
 */
public function getInvoiceForClientAndYear($clientId, $annee) {
    $stmt = $this->db->prepare("
        SELECT * FROM factures_annuelles
        WHERE client_id = :client_id AND annee = :annee
    ");
    
    $stmt->execute([
        'client_id' => $clientId,
        'annee' => $annee
    ]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Replace the getAllFacturesAnnuellesWithSignificantEcart method
public function getAllFacturesAnnuellesWithSignificantEcart() {
    $stmt = $this->db->prepare("
        SELECT fa.*, c.full_name, ca.ecart, ca.consommation_attendue, ca.consommation_reelle
        FROM factures_annuelles fa
        JOIN clients c ON fa.client_id = c.client_id
        JOIN consommations_annuelles ca ON fa.consommation_annuelle_id = ca.consommation_annuelle_id
        WHERE ABS(ca.ecart) > 50
        ORDER BY fa.date_emission DESC
    ");
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}