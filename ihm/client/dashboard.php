<?php
session_start();
// Vérifier si l'utilisateur est connecté et qu'il s'agit d'un client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Client - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .stat-card h3 {
            margin-bottom: 10px;
            color: var(--dark-color);
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }

        .consumption-chart {
            height: 300px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }

        .notification {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .notification:last-child {
            border-bottom: none;
        }

        .notification i {
            font-size: 18px;
            color: var(--secondary-color);
            margin-right: 15px;
            margin-top: 3px;
        }

        .notification-content h3 {
            font-size: 16px;
            margin: 0 0 5px 0;
        }

        .notification-date {
            color: #6c757d;
            font-size: 12px;
            display: block;
            margin-top: 5px;
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
                <a href="consumption.php">
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
            <h1>Tableau de Bord</h1>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Dernière Consommation</h3>
                    <div class="value">325 kWh</div>
                    <p>Novembre 2023</p>
                </div>
                <div class="stat-card">
                    <h3>Dernière Facture</h3>
                    <div class="value">32,500 XOF</div>
                    <p>Novembre 2023</p>
                </div>
                <div class="stat-card">
                    <h3>Moyenne Annuelle</h3>
                    <div class="value">310 kWh</div>
                    <p>Année 2023</p>
                </div>
            </div>

            <!-- Autres éléments spécifiques du dashboard peuvent être ajoutés ici -->
        </div>
    </div>
</body>

</html>