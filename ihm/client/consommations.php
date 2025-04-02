<?php
session_start();

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../connexion.php');
    exit;
}
require_once '../../traitement/consommationService.php';
require_once '../../models/consommationMensuelle.php';
require_once '../../DB/ClientDAO.php';

$clientDAO = new ClientDAO();
$client = $clientDAO->getClientByUserId($_SESSION['user_id']);

$consommationService = new ConsommationService();
$lastConsumption = $consommationService->getLastMonthlyConsumption($client->getClientId());
$consumptionDate = !empty($lastConsumption) ? date('F Y', strtotime($lastConsumption->getCreatedAt())) : 'N/A';
$consumptionValue = !empty($lastConsumption) ? $lastConsumption->getKw() : 0;
$imagePath = $lastConsumption ? $lastConsumption->getImagePath() : '';

// Handle form submission
$message = '';
$messageType = '';

/*This code processes a form submission that includes both a meter reading value and a photo of the meter, saves the photo to the server, and creates a new consumption record with both pieces of information.*/
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
                echo "<p>photo saved to directory and object created now calling the service to persist the consumption</p>";
                $result = $consommationService->submitConsommationMensuelle($_SESSION['user_id'], $consumption);
                if ($result) {
                    header('Location: dashboard.php');
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
        .consumption-form {
            max-width: 800px;
            margin: 0 auto;
        }

        .previous-reading {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .previous-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .previous-value {
            flex: 1;
        }

        .previous-value .value {
            font-size: 24px;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .previous-photo {
            flex: 1;
            text-align: right;
        }

        .meter-photo {
            width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        .upload-container {
            margin-top: 20px;
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .upload-preview {
            margin-top: 20px;
            display: none;
        }

        .upload-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 5px;
        }

        .summary-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .summary-total {
            font-weight: bold;
            font-size: 18px;
            color: var(--secondary-color);
        }
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
                <a href="dashboard.php" class="active">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="consommations.php">
                    <i class="fas fa-bolt"></i> Saisie Consommation
                </a>
                <a href="reclamations.php">
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
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <div class="card-header">
                    <h2>Novembre 2023</h2>
                </div>

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
                            <input type="number" name="current-value" id="current-value" data-previous-value="<?php echo $consumptionValue; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Photo du compteur</label>
                            <div class="upload-container">
                                <i class="fas fa-camera fa-2x"></i>
                                <p>Déposez votre photo ici ou</p>
                                <input type="file" name="meter-photo" id="meter-photo" accept="image/*" required>
                            </div>
                            <div class="upload-preview" id="photo-preview">
                                <img src="" alt="Aperçu">
                                <button type="button" class="btn btn-danger mt-1" id="remove-photo-btn">
                                    Supprimer
                                </button>
                            </div>
                        </div>

                        <div class="summary-card" id="estimation-summary" style="display: none;">
                            <h3>Estimation de facturation</h3>
                            <div class="summary-items">
                                <div class="summary-item">
                                    <span>Consommation</span>
                                    <span id="consumption-value">0 kWh</span>
                                </div>
                                <div class="summary-item">
                                    <span>Prix HT</span>
                                    <span id="price-ht">0 XOF</span>
                                </div>
                                <div class="summary-item">
                                    <span>TVA (18%)</span>
                                    <span id="price-tva">0 XOF</span>
                                </div>
                                <div class="summary-item summary-total">
                                    <span>Total TTC</span>
                                    <span id="price-ttc">0 XOF</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions text-center mt-3">
                            <button type="button" class="btn btn-secondary" id="calculate-btn">
                                Calculer l'estimation
                            </button>
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
    </div>9
    
    <script src="../../assets/js/main.js"></script>
    <script src="js/consumption.js"></script>
    
</body>

</html>