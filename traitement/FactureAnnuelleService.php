<?php
require_once __DIR__ . '/../DB/FactureAnnuelleDAO.php';
require_once __DIR__ . '/../DB/ConsommationAnnuelleDAO.php';
require_once __DIR__ . '/../DB/ClientDAO.php';
require_once __DIR__ . '/../models/FactureAnnuelle.php';

class FactureAnnuelleService {
    private $factureAnnuelleDAO;
    private $consommationAnnuelleDAO;
    private $clientDAO;
    
    public function __construct() {
        $this->factureAnnuelleDAO = new FactureAnnuelleDAO();
        $this->consommationAnnuelleDAO = new ConsommationAnnuelleDAO();
        $this->clientDAO = new ClientDAO();
    }
    
    // Generate annual invoice from annual consumption data
    // Update the generateFactureAnnuelle method
public function generateFactureAnnuelle($clientId, $annee) {
    // Get annual consumption data
    $consommationAnnuelle = $this->consommationAnnuelleDAO->getParClientEtAnnee($clientId, $annee);
    
    if (!$consommationAnnuelle) {
        throw new Exception("Données de consommation annuelle non disponibles pour ce client et cette année");
    }
    
    if ($consommationAnnuelle->getConsommationReelle() === null) {
        throw new Exception("La consommation réelle n'a pas encore été calculée pour cette année");
    }
    
    // Get the consumption difference (ecart)
    $ecart = abs($consommationAnnuelle->getEcart());
    
    // Calculate total amount based on the consumption difference (ecart)
    $montantTotal = $this->calculateAnnualTotal($ecart);
    
    // Create and save invoice
    $factureAnnuelle = new FactureAnnuelle(
        $clientId,
        $consommationAnnuelle->getId(),
        $annee,
        $montantTotal,
        $consommationAnnuelle->getConsommationReelle()
    );
    
    return $this->factureAnnuelleDAO->creerFacture($factureAnnuelle);
}
    // Calculate annual invoice amount
    private function calculateAnnualTotal($consommationTotale) {
        // Base calculation on total consumption
        $montantHT = 0;
        
        // Calculate based on consumption tiers (yearly rates)
        if ($consommationTotale <= 1200) { // Up to 1200 kWh per year
            $montantHT = $consommationTotale * 0.80;
        } elseif ($consommationTotale <= 1800) { // 1200-1800 kWh per year
            $montantHT = (1200 * 0.80) + (($consommationTotale - 1200) * 0.90);
        } else { // Over 1800 kWh per year
            $montantHT = (1200 * 0.80) + (600 * 0.90) + (($consommationTotale - 1800) * 1.05);
        }
        
        // Calculate TVA (18%)
        $tva = $montantHT * 0.18;
        
        // Final amount
        $montantTTC = $montantHT + $tva;
        
        // Round to 2 decimal places
        return round($montantTTC, 2);
    }
    
    // Get invoice details by ID
    public function getFactureById($id) {
        return $this->factureAnnuelleDAO->getById($id);
    }
    
    // Get all invoices for a client
    public function getFacturesAnnuellesByClient($clientId) {
        return $this->factureAnnuelleDAO->getFacturesAnnuellesByClientId($clientId);
    }
    
    // Get all invoices (for admin)
    public function getAllFacturesAnnuelles() {
        // Get only annual invoices where ecart > 50
        return $this->factureAnnuelleDAO->getAllFacturesAnnuellesWithSignificantEcart();
    }
    /**
 * Check if an invoice already exists for a client and year
 * 
 * @param int $clientId Client ID
 * @param int $annee Year
 * @return array|null The invoice data if it exists, null otherwise
 */
public function getInvoiceForClientAndYear($clientId, $annee) {
    return $this->factureAnnuelleDAO->getInvoiceForClientAndYear($clientId, $annee);
}
}