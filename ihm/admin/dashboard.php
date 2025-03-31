<?php
session_start();
// Vérifier si l'utilisateur est connecté et qu'il s'agit d'un fournisseur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header('Location: ../connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .stat-card.primary .icon {
            color: var(--secondary-color);
        }
        
        .stat-card.success .icon {
            color: var(--success-color);
        }
        
        .stat-card.warning .icon {
            color: var(--warning-color);
        }
        
        .stat-card.danger .icon {
            color: var(--danger-color);
        }
        
        .dashboard-charts {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
        }
        
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        .recent-activities {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .activity-item {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .activity-icon.blue {
            color: var(--secondary-color);
        }
        
        .activity-icon.green {
            color: var(--success-color);
        }
        
        .activity-icon.red {
            color: var(--danger-color);
        }
        
        .activity-content {
            flex-grow: 1;
        }
        
        .activity-title {
            font-weight: 500;
            margin-bottom: 3px;
        }
        
        .activity-time {
            color: #6c757d;
            font-size: 12px;
        }
        
        .import-section {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .import-zone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .import-zone:hover {
            background-color: #e9ecef;
            border-color: var(--secondary-color);
        }
        
        .import-zone i {
            font-size: 48px;
            color: #adb5bd;
            margin-bottom: 15px;
        }
        
        .import-zone p {
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .dashboard-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-charts {
                grid-template-columns: 1fr;
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
                <a href="claims.php">
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
            <h1>Tableau de Bord</h1>
            
            <div class="dashboard-stats">
                <div class="stat-card primary">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="value">1,245</div>
                    <div class="label">Clients</div>
                </div>
                <div class="stat-card success">
                    <div class="icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="value">42.5 M</div>
                    <div class="label">XOF facturés (mois)</div>
                </div>
                <div class="stat-card warning">
                    <div class="icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="value">385,620</div>
                    <div class="label">kWh consommés (mois)</div>
                </div>
                <div class="stat-card danger">
                    <div class="icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="value">24</div>
                    <div class="label">Réclamations en attente</div>
                </div>
            </div>
            
            <!-- Section d'importation (si nécessaire) -->
            <div class="import-section">
                <h2>Importation de la Consommation Annuelle</h2>
                <p>Importez le fichier de données de consommation annuelle pour mettre à jour les statistiques.</p>
                <div class="import-zone" id="import-zone">
                    <i class="fas fa-file-upload"></i>
                    <p>Glisser-déposer votre fichier ici ou cliquer pour parcourir</p>
                    <input type="file" id="file-upload" style="display: none;">
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-file-import"></i> Importer les données
                </button>
            </div>
            
            <!-- Vous pouvez ajouter ici d'autres sections spécifiques au dashboard fournisseur -->
        </div>
    </div>
    
    <script src="../../assets/js/main.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>