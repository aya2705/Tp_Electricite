<?php
require_once __DIR__ . '/../DB/FactureAnnuelleDAO.php';
require_once __DIR__ . '/../DB/ConsommationAnnuelleDAO.php';
require_once __DIR__ . '/../DB/ClientDAO.php';
require_once __DIR__ . '/../models/FactureAnnuelle.php';
require_once __DIR__ . '/notificationService.php';  // Add this line

class FactureAnnuelleService {
    private $factureAnnuelleDAO;
    private $consommationAnnuelleDAO;
    private $clientDAO;
    private $notificationService;  // Add this line
    
    public function __construct() {
        $this->factureAnnuelleDAO = new FactureAnnuelleDAO();
        $this->consommationAnnuelleDAO = new ConsommationAnnuelleDAO();
        $this->clientDAO = new ClientDAO();
        $this->notificationService = new NotificationService();  // Add this line
    }
    
    // Generate annual invoice from annual consumption data
    public function generateFactureAnnuelle($clientId, $annee) {
        // Get annual consumption data
        $consommationAnnuelle = $this->consommationAnnuelleDAO->getParClientEtAnnee($clientId, $annee);
        
        if (!$consommationAnnuelle) {
            throw new Exception("Données de consommation annuelle non disponibles pour ce client et cette année");
        }
        
        if ($consommationAnnuelle->getConsommationReelle() === null) {
            throw new Exception("La consommation réelle n'a pas encore été calculée pour cette année");
        }
        
        // Get the raw ecart (can be positive or negative)
        $ecart = $consommationAnnuelle->getEcart();
        $ecartAbs = abs($ecart);
        
        // Calculate total amount based on the absolute difference
        $montantTotal = $this->calculateAnnualTotal($ecartAbs);
        
        // Set invoice type based on ecart direction
        $type = ($ecart >= 0) ? 'credit' : 'debit';
        
        // Create and save invoice
        $factureAnnuelle = new FactureAnnuelle(
            $clientId,
            $consommationAnnuelle->getId(),
            $annee,
            $montantTotal,
            $consommationAnnuelle->getConsommationReelle(),
            $type
        );
        
        $result = $this->factureAnnuelleDAO->creerFacture($factureAnnuelle);
        
        // Add notification for the annual invoice
        $notificationMessage = ($type === 'credit') 
            ? "Une facture annuelle de régularisation (avoir) de {$montantTotal} MAD a été générée pour l'année {$annee}."
            : "Une facture annuelle de régularisation de {$montantTotal} MAD a été générée pour l'année {$annee}.";
            
        $this->notificationService->addNotification(
            $clientId,
            'facture',
            'FA-' . str_pad($factureAnnuelle->getFactureId(), 6, '0', STR_PAD_LEFT),
            $notificationMessage
        );
        
        return $result;
    }
    // Calculate annual invoice amount
    
// Calculate annual invoice amount based on ecart (difference)
public function calculateAnnualTotal($ecart) {
    // Base calculation on ecart (difference amount)
    $montantHT = 0;
    
    // Calculate based on ecart tiers
    if ($ecart <= 100) { // Small discrepancy
        $montantHT = $ecart * 1.00; // 1 MAD per kWh
    } elseif ($ecart <= 500) { // Medium discrepancy
        $montantHT = (100 * 1.00) + (($ecart - 100) * 0.90); // 0.90 MAD per kWh after first 100
    } else { // Large discrepancy
        $montantHT = (100 * 1.00) + (400 * 0.90) + (($ecart - 500) * 0.80); // 0.80 MAD per kWh after 500
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