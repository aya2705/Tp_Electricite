<?php
session_start();

require_once '../../traitement/consommationService.php';
$consommationService = new ConsommationService();
$consumptionDate = 'null';
$consumptionValue = 0;
$imagePath = 'null';
$clientId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $anomalyId = $_GET['id'];
        $kw = filter_input(INPUT_POST, 'current-value', FILTER_VALIDATE_FLOAT);
        // Récupérer l'ID client à partir du champ caché du formulaire
        $clientId = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_STRING);
        if (empty($clientId)) {
            throw new Exception("ID client manquant");
        }
        $consommationService->treatMonthlyConsumptionWithAnomaly($clientId, $anomalyId, $kw);
        header('Location: factures.php');
    } catch (Exception $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'error';
    }
} else {
    $anomalyId = $_GET['id'];
    $consumptionToCorrect = $consommationService->getConsumptionByAnomalyId($anomalyId);
    // Récupérer l'ID client depuis les détails de l'anomalie
    $anomalyDetails = $consommationService->getAnomalyDetails($anomalyId);
    $clientId = $anomalyDetails['client_id'] ?? null;
    
    $consumptionDate = !empty($consumptionToCorrect) ? date('F Y', strtotime($consumptionToCorrect->getCreatedAt())) : 'N/A';
    $consumptionValue = !empty($consumptionToCorrect) ? $consumptionToCorrect->getKw() : 0;
    $imagePath = $consumptionToCorrect ? $consumptionToCorrect->getImagePath() : '';
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Saisies - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Espace Fournisseur</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="clients.php">
                    <i class="fas fa-users"></i> Gestion des clients
                </a>
                <a href="factures.php" >
                    <i class="fas fa-file-invoice"></i> Gestion des factures
                </a>
                <a href="consumption.php" class="active">
                    <i class="fas fa-bolt"></i> Gestion des saisies
                </a>
                <a href="claims-frs.php">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="settings.php" >
                    <i class="fas fa-cog"></i> Paramètres
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Rectification des Consommations</h1>

            <div class="card">

                <div class="consumption-form">
                    <div class="previous-reading">
                        <h3>Consommation</h3>
                        <div class="previous-details">
                            <div class="previous-value">
                                <p>Relevé du compteur:</p>
                                <div class="value"><?php echo $consumptionValue; ?> kWh</div>
                                <p>Date de relevé: <?php echo $consumptionDate; ?></p>
                            </div>
                            <div class="previous-photo">
                                <img src="<?php echo htmlspecialchars($imagePath); ?>"  
                                     class="meter-photo"                  
                                     onclick="openPhotoModal(this.src)">
                                <p><small>Cliquer pour agrandir</small></p>
                            </div>
                        </div>
                    </div>

                    <form id="consumption-form" method="POST" action="correction.php?id=<?php echo $anomalyId ?>" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="current-value">Valeur correcte (kWh)</label>
                            <input type="number" name="current-value" id="current-value" required>
                            <!-- Champ caché pour l'ID client -->
                            <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($clientId); ?>">
                        </div>
                        <div class="form-actions text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                Soumettre la consommation correcte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Modal pour afficher la photo en grand -->
<div id="photo-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Photo du compteur</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <img src="" id="modal-image" style="width: 100%;">
            </div>
        </div>
    </div>
    <script src="../../assets/js/main.js"></script>
</body>

</html>