<?php
// Enable error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// filepath: c:\wamp64\www\TP2\traitement\agentService.php
session_start();
require_once __DIR__ . '/../DB/ConsommationAnnuelleDAO.php';
require_once __DIR__ . '/../DB/consommationDAO.php';
require_once __DIR__ . '/../DB/ClientDAO.php';
require_once __DIR__ . '/../models/ConsommationAnnuelle.php';

// Check if user is logged in and is an agent for action requests
if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'upload_consumption' && 
    (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent')) {
    header('Location: ../ihm/connexion.php');
    exit;
}

// Handle action requests (from forms, etc.)
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    
    switch ($action) {
        case 'upload_consumption':
            uploadConsumptionFile();
            break;
            
        default:
            header('Location: ../ihm/agent/dashboard.php?error=Action invalide');
            exit;
    }
}

/**
 * Process uploaded consumption file and save data
 */
function uploadConsumptionFile() {
    // Validate form data
    if (!isset($_POST['annee'])) {
        header('Location: ../ihm/agent/dashboard.php?error=Année manquante');
        exit;
    }
    
    $annee = filter_input(INPUT_POST, 'annee', FILTER_VALIDATE_INT);
    if (!$annee) {
        header('Location: ../ihm/agent/dashboard.php?error=Année invalide');
        exit;
    }
    
    // Validate file upload
    if (!isset($_FILES['consumption_file']) || $_FILES['consumption_file']['error'] !== UPLOAD_ERR_OK) {
        $error = $_FILES['consumption_file']['error'] ?? 'Erreur inconnue';
        header("Location: ../ihm/agent/dashboard.php?error=Erreur de téléchargement: $error");
        exit;
    }
    
    // Check file type
    $fileInfo = pathinfo($_FILES['consumption_file']['name']);
    if (strtolower($fileInfo['extension']) !== 'txt') {
        header('Location: ../ihm/agent/dashboard.php?error=Seuls les fichiers .txt sont acceptés');
        exit;
    }
    
    try {
        // Process the file
        $filePath = $_FILES['consumption_file']['tmp_name'];
        error_log("File path: " . $filePath);
        error_log("File content: " . file_get_contents($filePath));
        
        $result = processConsumptionFile($filePath, $annee);
        
        // Prepare success message with details
        $message = "{$result['processed']} enregistrements traités avec succès. ";
        $message .= "{$result['clientsWithData']} clients ont des données mensuelles pour comparaison. ";
        
        if ($result['significantDiscrepancies'] > 0) {
            $message .= "{$result['significantDiscrepancies']} écarts significatifs détectés.";
        }
        
        if (!empty($result['errors'])) {
            $message .= " " . count($result['errors']) . " erreurs rencontrées.";
        }
        
        header("Location: ../ihm/agent/dashboard.php?success=" . urlencode($message));
        exit;
    } catch (Exception $e) {
        error_log("Exception in file processing: " . $e->getMessage());
        header('Location: ../ihm/agent/dashboard.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

/**
 * Business logic function to process consumption file
 */
function processConsumptionFile($filePath, $annee) {
    error_log("Processing file for year: $annee");
    error_log("File path: $filePath");
    
    $consommationAnnuelleDAO = new ConsommationAnnuelleDAO();
    $consommationDAO = new ConsommationDAO();
    $clientDAO = new ClientDAO();
    
    $result = [
        'processed' => 0,
        'clientsWithData' => 0,
        'significantDiscrepancies' => 0,
        'errors' => []
    ];
    
    // Read file content
    $fileContent = file_get_contents($filePath);
    if ($fileContent === false) {
        throw new Exception("Impossible de lire le contenu du fichier");
    }
    
    error_log("File content: " . $fileContent);
    
    // Process each line
    $lines = explode("\n", $fileContent);
    
    foreach ($lines as $lineNum => $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        error_log("Processing line $lineNum: $line");
        
        // Parse line (format: clientId,expectedConsumption)
        $parts = explode(',', $line);
        if (count($parts) !== 2) {
            $result['errors'][] = "Format invalide dans la ligne: $line";
            continue;
        }
        
        $clientId = trim($parts[0]);
        $consommationAttendue = floatval(trim($parts[1]));
        
        if (empty($clientId) || $consommationAttendue <= 0) {
            $result['errors'][] = "Données invalides dans la ligne: $line";
            continue;
        }
        
        // Verify client exists
        $client = $clientDAO->getClientById($clientId);
        if (!$client) {
            $result['errors'][] = "Client ID $clientId n'existe pas";
            continue;
        }
        
        error_log("Client found: " . $client->getFullName() . " with ID: $clientId");
        
        try {
            // Create ConsommationAnnuelle object
            $consommationAnnuelle = new ConsommationAnnuelle(
                $clientId, 
                $annee, 
                $consommationAttendue
            );
            
            // Get actual consumption from monthly data
            $consommationReelle = $consommationDAO->calculerConsommationTotaleAnnuelle($clientId, $annee);
            error_log("Actual consumption for client $clientId in year $annee: $consommationReelle");
            
            if ($consommationReelle > 0) {
                $result['clientsWithData']++;
                $consommationAnnuelle->setConsommationReelle($consommationReelle);
                
                // Check for significant discrepancy
                if ($consommationAnnuelle->aEcartSignificatif(5)) {
                    $result['significantDiscrepancies']++;
                    error_log("Significant discrepancy detected for client $clientId");
                }
            }
            
            // Save to database
            $consommationAnnuelleDAO->enregistrer($consommationAnnuelle);
            $result['processed']++;
            error_log("Successfully saved consumption for client $clientId");
            
        } catch (Exception $e) {
            error_log("Error processing client $clientId: " . $e->getMessage());
            $result['errors'][] = "Erreur avec le client ID $clientId: " . $e->getMessage();
        }
    }
    
    return $result;
}

/**
 * Get annual consumptions with significant discrepancies
 * 
 * @param int $year Optional year to filter by
 * @param float $threshold Percentage threshold for discrepancies
 * @return array List of consumptions with discrepancies
 */
function getConsommationsAvecEcartsSignificatifs($year = null, $threshold = 5) {
    $consommationAnnuelleDAO = new ConsommationAnnuelleDAO();
    
    // Fetch all consumption records with discrepancies
    $consommations = $consommationAnnuelleDAO->getTousAvecEcarts();
    
    // Filter by year if specified
    if ($year !== null) {
        $consommations = array_filter($consommations, function($c) use ($year) {
            return $c->getAnnee() == $year;
        });
    }
    
    // Filter by threshold
    return array_filter($consommations, function($c) use ($threshold) {
        return $c->aEcartSignificatif($threshold);
    });
}