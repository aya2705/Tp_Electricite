<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../../traitement/FactureAnnuelleService.php';
require_once '../../DB/ConsommationAnnuelleDAO.php';
require_once '../../DB/ClientDAO.php';

$consommationAnnuelleDAO = new ConsommationAnnuelleDAO();
$clientDAO = new ClientDAO();
$factureAnnuelleService = new FactureAnnuelleService();

// Get all clients
$clients = $clientDAO->getAllClients();

// Handle invoice generation
$message = null;
$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $clientId = filter_input(INPUT_POST, 'client_id', FILTER_VALIDATE_INT);
    $annee = filter_input(INPUT_POST, 'annee', FILTER_VALIDATE_INT);
    
    if ($clientId && $annee) {
        try {
            $factureAnnuelle = $factureAnnuelleService->generateFactureAnnuelle($clientId, $annee);
            $message = "Facture annuelle générée avec succès pour le client #$clientId, année $annee";
        } catch (Exception $e) {
            $errorMessage = "Erreur: " . $e->getMessage();
        }
    } else {
        $errorMessage = "Veuillez sélectionner un client et une année valides";
    }
}

// Years for dropdown (current year and 5 years back)
$years = range(date('Y'), date('Y') - 5);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Génération des Factures Annuelles - Gestion des Factures</title>
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
                <a href="factures.php">
                    <i class="fas fa-file-invoice"></i> Gestion des factures
                </a>
                <a href="generation-factures-annuelles.php" class="active">
                    <i class="fas fa-file-invoice-dollar"></i> Factures Annuelles
                </a>
                <a href="consumption.php">
                    <i class="fas fa-bolt"></i> Gestion des saisies
                </a>
                <a href="claims-frs.php">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Génération des Factures Annuelles</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h2>Générer une facture annuelle</h2>
                </div>
                <div class="card-body">
                    <form method="post" action="" class="form">
                        <div class="form-group">
                            <label for="client_id">Client:</label>
                            <select name="client_id" id="client_id" class="form-control" required>
                                <option value="">-- Sélectionnez un client --</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client->getClientId(); ?>">
                                        <?php echo htmlspecialchars($client->getFullName()) . ' (#' . $client->getClientId() . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="annee">Année:</label>
                            <select name="annee" id="annee" class="form-control" required>
                                <option value="">-- Sélectionnez une année --</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="generate" class="btn btn-primary">
                                <i class="fas fa-file-invoice"></i> Générer la facture
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="instructions mt-4">
                <h3>Instructions</h3>
                <p>Cette page permet de générer des factures annuelles pour les clients à partir des données de consommation annuelle.</p>
                <ul>
                    <li>Assurez-vous qu'une consommation annuelle réelle existe pour le client et l'année sélectionnés</li>
                    <li>Une facture ne peut être générée qu'une seule fois pour un client et une année donnés</li>
                    <li>Le calcul du montant tient compte des tarifs annuels applicables en fonction de la consommation</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    });
    </script>
</body>
</html>