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
require_once '../../traitement/FactureAnnuelleService.php';
$factureAnnuelleService = new FactureAnnuelleService();
$facturesAnnuelles = $factureAnnuelleService->getAllFacturesAnnuelles();

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
            <div class="tab-navigation">
            <button class="tab-button active" data-target="monthly-invoices-content">Factures Mensuelles</button>
            <button class="tab-button" data-target="annual-invoices-content">Factures Annuelles</button>
            </div>
            <div class="tab-content" id="monthly-invoices-content">
            <!-- Table des factures -->
            <div class="card">
                
                <div class="card-body">
                    <table class="data-table">
                    <thead>
    <tr>
        <th>Client ID</th>  <!-- New column for client ID -->
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
                <td><?php echo $facture['client_id']; ?></td>  <!-- Display client ID -->
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
            <div class="tab-content" id="annual-invoices-content" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h2>Factures Annuelles</h2>
        </div>
        <div class="card-body">
        <table class="data-table">
    <!-- Replace the annual invoices table header and rows in factures.php around line 178 -->
<thead>
    <tr>
        <th>Année</th>
        <th>N° Facture</th>
        <th>Client</th>
        <th>Consommation Attendue</th>
        <th>Consommation Réelle</th>
        <th>Écart (kWh)</th>
        <th>Écart Facturé</th>
        <th>Montant</th>
        <th>Date émission</th>
        <th>Statut</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    <?php if (!empty($facturesAnnuelles)): ?>
        <?php foreach ($facturesAnnuelles as $facture): ?>
            <?php 
                // Style for ecart
                $ecartClass = abs($facture['ecart']) > 100 ? 'badge-anomaly' : 'badge-warning';
                // Calculate absolute ecart that was used for billing
                $ecartFacture = abs($facture['ecart']);
            ?>
            <tr>
                <td><?php echo $facture['annee']; ?></td>
                <td>FA-<?php echo str_pad($facture['facture_annuelle_id'], 6, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo htmlspecialchars($facture['full_name']); ?></td>
                <td><?php echo number_format($facture['consommation_attendue'], 0) . " kWh"; ?></td>
                <td><?php echo number_format($facture['consommation_reelle'], 0) . " kWh"; ?></td>
                <td>
                    <span class="badge <?php echo $ecartClass; ?>">
                        <?php echo number_format($facture['ecart'], 0) . " kWh"; ?>
                    </span>
                </td>
                <td><?php echo number_format($ecartFacture, 0) . " kWh"; ?></td>
                <td><?php echo number_format($facture['montant_total'], 2) . " MAD"; ?></td>
                <td><?php echo date('d/m/Y', strtotime($facture['date_emission'])); ?></td>
                <td>
                    <span class="badge <?php echo $facture['statut'] === 'emise' ? 'badge-warning' : ($facture['statut'] === 'payee' ? 'badge-success' : 'badge-danger'); ?>">
                        <?php echo ucfirst($facture['statut']); ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="../../traitement/generate_annual_pdf.php?id=<?php echo $facture['facture_annuelle_id']; ?>"
                       class="btn btn-sm btn-info" target="_blank">
                        <i class="fas fa-download"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="11" class="text-center">Aucune facture annuelle disponible</td> <!-- Updated colspan to match column count -->
        </tr>
    <?php endif; ?>
</tbody>
</table>
        </div>
    </div>
</div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
    // Tab navigation
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });
                
                // Remove active class from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Show the target content and make button active
                const targetId = this.dataset.target;
                document.getElementById(targetId).style.display = 'block';
                this.classList.add('active');
            });
        });
    });
</script>
</body>

</html>