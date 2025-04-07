<?php
session_start();
// Vérifier que l'utilisateur est connecté et qu'il est fournisseur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../../traitement/StatistiquesService.php';
$statsService = new StatistiquesService();

$currentMonth = (int)date('m');
$currentYear  = (int)date('Y');

$clientsCount      = $statsService->getEligibleClientsCount();
$facturesAmount    = $statsService->getFacturesAmountForMonth($currentMonth, $currentYear);
$kwhConsumed       = $statsService->getConsumedKwhForMonth($currentMonth, $currentYear);
$pendingClaims     = $statsService->getPendingClaimsCount();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Fournisseur</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
            text-align: center;
        }
        .stat-card .icon {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--dark-color);
        }
        .stat-card .label {
            color: #6c757d;
            font-size: 14px;
        }
        .stat-card.primary .icon { color: var(--secondary-color); }
        .stat-card.success .icon { color: var(--success-color); }
        .stat-card.warning .icon { color: var(--warning-color); }
        .stat-card.danger .icon  { color: var(--danger-color); }
        
        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-stats {
                grid-template-columns: repeat(2, 1fr);
            }
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
                <a href="dashboard.php" class="active">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="clients.php">
                    <i class="fas fa-users"></i> Gestion des clients
                </a>
                <a href="factures.php">
                    <i class="fas fa-file-invoice"></i> Gestion des factures
                </a>
                <a href="consumption.php">
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
            <h1>Tableau de Bord</h1>
            <div class="dashboard-stats">
                <div class="stat-card primary">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="value"><?php echo number_format($clientsCount); ?></div>
                    <div class="label">Clients</div>
                </div>
                <div class="stat-card success">
                    <div class="icon"><i class="fas fa-file-invoice"></i></div>
                    <div class="value"><?php echo $facturesAmount; ?></div>
                    <div class="label">MAD facturés (mois)</div>
                </div>
                <div class="stat-card warning">
                    <div class="icon"><i class="fas fa-bolt"></i></div>
                    <div class="value"><?php echo number_format($kwhConsumed); ?></div>
                    <div class="label">kWh consommés (mois)</div>
                </div>
                <div class="stat-card danger">
                    <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="value"><?php echo number_format($pendingClaims); ?></div>
                    <div class="label">Réclamations en attente</div>
                </div>
            </div>
            
            <!-- Vous pouvez ajouter d'autres sections spécifiques ici -->
        </div>
    </div>
</body>
</html>