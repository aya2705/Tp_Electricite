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
                    <div class="label">MAD facturés (mois)</div>
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