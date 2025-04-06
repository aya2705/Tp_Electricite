<?php
session_start();

// Check if user is logged in and is a supplier
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../../traitement/agentService.php';
require_once '../../DB/ClientDAO.php';

// Get year filter
$year = isset($_GET['year']) ? filter_input(INPUT_GET['year'], FILTER_VALIDATE_INT) : date('Y');

// Get discrepancy threshold
$threshold = isset($_GET['threshold']) ? filter_input(INPUT_GET['threshold'], FILTER_VALIDATE_FLOAT) : 5.0;

// Get all annual consumption records with discrepancies
$discrepancies = getConsommationsAvecEcartsSignificatifs($year, $threshold);

// Get client names for display
$clientDAO = new ClientDAO();
$clientNames = [];
foreach ($discrepancies as $disc) {
    $client = $clientDAO->getClientById($disc->getClientId());
    if ($client) {
        $clientNames[$disc->getClientId()] = $client->getFullName();
    } else {
        $clientNames[$disc->getClientId()] = "Client #" . $disc->getClientId();
    }
}

// Years for filter dropdown
$years = range(date('Y'), date('Y') - 5);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparaison des Consommations - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 20px;
        }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-success { color: #28a745; }
        .filters {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
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
                <a href="claims-frs.php">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="consumption.php">
                    <i class="fas fa-bolt"></i> Gestion des saisies
                </a>
                <a href="consumption-comparison.php" class="active">
                    <i class="fas fa-chart-bar"></i> Comparaison Annuelle
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Comparaison des Consommations Annuelles</h1>
            
            <div class="filters">
                <form action="" method="get" class="filter-form">
                    <div class="filter-group">
                        <label for="year-filter">Année:</label>
                        <select id="year-filter" name="year" onchange="this.form.submit()">
                            <option value="">Toutes les années</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?php echo $y; ?>" <?php echo $year == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="threshold-filter">Seuil d'écart (%):</label>
                        <select id="threshold-filter" name="threshold" onchange="this.form.submit()">
                            <option value="1" <?php echo $threshold == 1 ? 'selected' : ''; ?>>1%</option>
                            <option value="5" <?php echo $threshold == 5 ? 'selected' : ''; ?>>5%</option>
                            <option value="10" <?php echo $threshold == 10 ? 'selected' : ''; ?>>10%</option>
                            <option value="15" <?php echo $threshold == 15 ? 'selected' : ''; ?>>15%</option>
                            <option value="20" <?php echo $threshold == 20 ? 'selected' : ''; ?>>20%</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="card">
                <h2>Écarts Significatifs de Consommation</h2>
                
                <?php if (empty($discrepancies)): ?>
                    <div class="alert alert-info">
                        Aucun écart significatif n'a été détecté avec les critères sélectionnés.
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Année</th>
                                <th>Consommation Attendue (kWh)</th>
                                <th>Consommation Réelle (kWh)</th>
                                <th>Écart (kWh)</th>
                                <th>Écart (%)</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($discrepancies as $disc): ?>
                                <?php 
                                    $ecart = $disc->getEcart();
                                    $ecartPct = abs($ecart / ($disc->getConsommationReelle() ?: 1) * 100);
                                    
                                    $ecartClass = '';
                                    if ($ecartPct > 15) {
                                        $ecartClass = 'text-danger';
                                    } elseif ($ecartPct > 5) {
                                        $ecartClass = 'text-warning';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($clientNames[$disc->getClientId()]); ?></td>
                                    <td><?php echo $disc->getAnnee(); ?></td>
                                    <td><?php echo number_format($disc->getConsommationAttendue(), 0, ',', ' '); ?></td>
                                    <td><?php echo number_format($disc->getConsommationReelle(), 0, ',', ' '); ?></td>
                                    <td class="<?php echo $ecartClass; ?>">
                                        <?php echo number_format($ecart, 0, ',', ' '); ?>
                                    </td>
                                    <td class="<?php echo $ecartClass; ?>">
                                        <?php echo number_format($ecartPct, 2, ',', ' '); ?>%
                                    </td>
                                    <td><?php echo $disc->getStatut(); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h3>Interprétation des écarts</h3>
                <p>Un écart positif signifie que la consommation attendue est supérieure à la consommation réelle mesurée.</p>
                <p>Un écart négatif signifie que le client a consommé plus que prévu par l'agent.</p>
                <p>Les écarts importants peuvent indiquer :</p>
                <ul>
                    <li>Des erreurs de relevé de compteur</li>
                    <li>Des changements dans les habitudes de consommation</li>
                    <li>Des équipements défectueux</li>
                    <li>Des problèmes potentiels de fraude</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>