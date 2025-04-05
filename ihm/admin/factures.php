<?php
session_start();
// Vérifier si l'utilisateur est connecté et qu'il s'agit d'un fournisseur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../../DB/FactureDAO.php';
$factureDAO = new FactureDAO();
$factures = $factureDAO->getAllFactures();
function getStatusClass($status) {
    $status = strtolower($status);
    switch($status) {
        case 'payée':
        case 'paye':
        case 'payee':
            return 'success';
        case 'en_retard':
        case 'retard':
            return 'warning';
        case 'impayée':
        case 'impaye':
        case 'impayee':
            return 'info';
        default:
            return 'secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Factures - Espace Fournisseur</title>
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
                <a href="factures.php" class="active">
                    <i class="fas fa-file-invoice"></i> Gestion des factures
                </a>
                <a href="claims-frs.php">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="consumption.php">
                    <i class="fas fa-bolt"></i> Gestion des saisies
                </a>
                <a href="settings.php">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Gestion des Factures</h1>
            
            <div class="filters">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher une facture..." id="search-facture">
                </div>
                <button class="btn btn-primary" onclick="generateFactures()">
                    <i class="fas fa-plus"></i> Générer les factures du mois
                </button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Facture</th>
                        <th>Client</th>
                        <th>Période</th>
                        <th>Consommation</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date émission</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($factures)): ?>
                        <?php foreach($factures as $facture): ?>
            <tr>
                <td>FAC-<?php echo str_pad($facture['facture_id'], 6, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo htmlspecialchars($facture['client_id']); ?></td>
                <td><?php echo date('m/Y', strtotime($facture['periode'])); ?></td>
                <td><?php echo number_format($facture['consommation']); ?> kWh</td>
                <td><?php echo number_format($facture['montant'], 2); ?> MAD</td>
                <td>
                    <span class="badge badge-<?php echo getStatusClass($facture['statut_paiement']); ?>">
                        <?php echo htmlspecialchars($facture['statut_paiement'] ?? 'impayée'); ?>
                    </span>
                </td>
                <td><?php echo date('d/m/Y', strtotime($facture['date_emission'])); ?></td>
                              
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Aucune facture trouvée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

  
</body>
</html>