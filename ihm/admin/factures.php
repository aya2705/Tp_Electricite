<?php
session_start();
// Vérifier si l'utilisateur est connecté et qu'il s'agit d'un fournisseur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header('Location: ../connexion.php');
    exit;
}
require_once '../../traitement/consommationService.php';
require_once '../../traitement/FactureMensuelleService.php';

// Initialize services
$factureMensuelleService = new FactureMensuelleService();
$consommationService = new ConsommationService();

// Get factures
$factures = $factureMensuelleService->getAllFacturesMensuelles();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Factures - Espace Fournisseur</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .filters {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .search-container {
            flex-grow: 1;
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }

        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        .card {
            padding: 0px;
        }
    </style>
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
            <h1>Gestion des Factures</h1>

            <div class="filters">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher une facture..." id="search-client">
                </div>
            </div>

            <!-- Table des factures -->
            <div class="card">
                
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Période</th>
                                <th>N° Facture</th>
                                <th>Consommation</th>
                                <th>Montant</th>
                                <th>Date émission</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($factures)): ?>
                                <?php foreach ($factures as $facture): ?>
                                    <tr>
                                        <td><?php echo date('m/Y', strtotime($facture['date_emission'])); ?></td>
                                        <td>FAC-<?php echo str_pad($facture['facture_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo number_format($facture['consommation']); ?> kWh</td>
                                        <td><?php echo number_format($facture['montant'], 2); ?> MAD</td>
                                        <td><?php echo date('d/m/Y', strtotime($facture['date_emission'])); ?></td>
                                        <td class="actions">
                                            <a href="../../traitement/generate_pdf.php?id=<?php echo $facture['facture_id']; ?>"
                                                class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Aucune facture disponible</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>

</html>