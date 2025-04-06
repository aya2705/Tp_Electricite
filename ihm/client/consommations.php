<?php
session_start();
// Vérifier que le client est authentifié via $_SESSION['user_id']
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../../traitement/consommationService.php';
require_once '../../models/consommationMensuelle.php';

$clientId = $_SESSION['client_id'];

$consommationService = new ConsommationService();
$lastConsumption = $consommationService->getLastMonthlyConsumption($clientId);
$consumptionDate = !empty($lastConsumption) ? date('F Y', strtotime($lastConsumption->getCreatedAt())) : 'N/A';
$consumptionValue = !empty($lastConsumption) ? $lastConsumption->getKw() : 0;
$imagePath = $lastConsumption ? $lastConsumption->getImagePath() : '';

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $kw = filter_input(INPUT_POST, 'current-value', FILTER_VALIDATE_FLOAT);
        $imagePath = null;
        
        if (isset($_FILES['meter-photo']) && $_FILES['meter-photo']['error'] === 0) {
            $uploadDir = '../../uploads/meters/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . $_FILES['meter-photo']['name'];
            $imagePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['meter-photo']['tmp_name'], $imagePath)) {
                $consumption = new ConsommationMensuelle($kw, $imagePath);
                // echo $consumption->getKw();
                // echo "<p>photo saved to directory and object created now calling the service to persist the consumption</p>";
                $result = $consommationService->submitConsommationMensuelle($clientId, $consumption);
                 
                if ($result) {
                    // echo "Good";
                    header('Location: consommations.php');
                    exit;
                }
            } else {
                throw new Exception('Erreur lors du téléchargement de l\'image');
            }
        }
    } catch (Exception $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Format date for display
$consumptionDate = !empty($lastConsumption) ? date('F Y', strtotime($lastConsumption->getCreatedAt())) : 'N/A';
$consumptionValue = !empty($lastConsumption) ? $lastConsumption->getKw() : 0;
$imagePath = $lastConsumption ? $lastConsumption->getImagePath() : '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie de Consommation - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Espace Client</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="consommations.php" class="active">
                    <i class="fas fa-bolt"></i> Saisie Consommation
                </a>
                <a href="claims.php">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="profile.php">
                    <i class="fas fa-user"></i> Mon Profil
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Saisie de Consommation</h1>
            <div class="card">
                <div class="consumption-form">
                    <div class="previous-reading">
                        <h3>Dernière Lecture</h3>
                        <div class="previous-details">
                            <div class="previous-value">
                                <p>Relevé du compteur:</p>
                                <div class="value"><?php echo $consumptionValue; ?> kWh</div>
                                <p>Date de relevé: <?php echo $consumptionDate; ?></p>
                            </div>
                            <div class="previous-photo">
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                     alt="Photo du compteur précédent" 
                                     class="meter-photo"                  
                                     onclick="openPhotoModal(this.src)">
                                <p><small>Cliquer pour agrandir</small></p>
                            </div>
                        </div>
                    </div>

                    <form id="consumption-form" method="POST" action="consommations.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="current-value">Valeur actuelle du compteur (kWh)</label>
                            <input type="number" name="current-value" id="current-value" required>
                        </div>
                        <div class="form-group">
                            <label>Photo du compteur</label>
                            <div class="upload-container">
                                <i class="fas fa-camera fa-2x"></i>
                                <p>Déposez votre photo ici</p>
                                <input type="file" name="meter-photo" id="meter-photo" accept="image/*" required>
                            </div>
                        </div>
                        <div class="form-actions text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                Soumettre la saisie
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
    
    <script src="assets/js/main.js"></script>
    <script src="js/consumption.js"></script>
    
</body>

</html>