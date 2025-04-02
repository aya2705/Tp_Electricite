<?php
session_start();
// Vérifier si l'utilisateur est connecté et qu'il s'agit d'un client

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../../traitement/consommationService.php';

$consommationService = new ConsommationService();
$lastConsumption = $consommationService->getLastMonthlyConsumption($_SESSION['user_id']);

// Formatage de la date pour l'affichage
$consumptionDate = !empty($lastConsumption) ? date('F Y', strtotime($lastConsumption->getCreatedAt())) : 'N/A';
$consumptionValue = !empty($lastConsumption) ? $lastConsumption->getKw() : 0;
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
            <h1>Tableau de Bord</h1>
            
            <!-- Dernière consommation -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Dernière Consommation</h3>
                    <div class="value"><?php echo $consumptionValue; ?> kWh</div>
                    <p><?php echo $consumptionDate; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Dernière Facture</h3>
                    <div class="value">32,500 MAD</div>
                    <p>Novembre 2023</p>
                </div>
                <div class="stat-card">
                    <h3>Moyenne Annuelle</h3>
                    <div class="value">310 kWh</div>
                    <p>Année 2023</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Factures Récentes</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Référence</th>
                            <th>Consommation</th>
                            <th>Montant TTC</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>15/11/2023</td>
                            <td>FACT-2023-11</td>
                            <td>325 kWh</td>
                            <td>32,500 MAD</td>
                            <td><span class="status-badge status-paid">Payée</span></td>
                            <td>
                                <a href="#" class="download-btn"><i class="fas fa-file-pdf"></i> PDF</a>
                            </td>
                        </tr>
                        <tr>
                            <td>15/10/2023</td>
                            <td>FACT-2023-10</td>
                            <td>315 kWh</td>
                            <td>31,500 MAD</td>
                            <td><span class="status-badge status-paid">Payée</span></td>
                            <td>
                                <a href="#" class="download-btn"><i class="fas fa-file-pdf"></i> PDF</a>
                            </td>
                        </tr>
                        <tr>
                            <td>15/09/2023</td>
                            <td>FACT-2023-09</td>
                            <td>330 kWh</td>
                            <td>33,000 MAD</td>
                            <td><span class="status-badge status-paid">Payée</span></td>
                            <td>
                                <a href="#" class="download-btn"><i class="fas fa-file-pdf"></i> PDF</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center mt-2">
                    <a href="#" class="btn btn-secondary">Voir toutes les factures</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Notifications</h2>
                </div>
                <div class="notification-list">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <div class="notification-content">
                            <h3>Saisie de consommation disponible</h3>
                            <p>Vous pouvez maintenant saisir votre consommation pour le mois de Novembre 2023.</p>
                            <span class="notification-date">Aujourd'hui</span>
                        </div>
                    </div>
                    <div class="notification">
                        <i class="fas fa-check-circle"></i>
                        <div class="notification-content">
                            <h3>Réclamation traitée</h3>
                            <p>Votre réclamation #REF-2023-42 a été traitée. Consultez les détails.</p>
                            <span class="notification-date">Il y a 2 jours</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>