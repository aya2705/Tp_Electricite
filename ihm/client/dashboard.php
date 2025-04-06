<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../../traitement/consommationService.php';
require_once '../../traitement/FactureMensuelleService.php';
require_once '../../traitement/notificationService.php';

$clientId = $_SESSION['client_id'];

// Initialize services
$factureMensuelleService = new FactureMensuelleService();
$consommationService = new ConsommationService();

// Get client data
$factures = $factureMensuelleService->getAllFacturesByClient($clientId);
$lastFacture = $factureMensuelleService->getLastFactureMensuelleByClient($clientId);
$lastConsumption = $consommationService->getLastMonthlyConsumption($clientId);

// Format data for display
$consumptionDate = !empty($lastConsumption) ? date('F Y', strtotime($lastConsumption->getCreatedAt())) : 'N/A';
$consumptionValue = !empty($lastConsumption) ? $lastConsumption->getKw() : 0;
$lastFactureMontant = !empty($lastFacture) ? number_format($lastFacture['montant'], 2) : '0.00';
$lastFacturePeriode = !empty($lastFacture) ? date('F Y', strtotime($lastFacture['date_emission'])) : 'N/A';

// Utiliser la classe NotificationDAO
$notificationDAO = new NotificationDAO();
$notifications = $notificationDAO->getUnreadNotifications($clientId);
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

        /* Notif */

        .card h2 {
            font-size: 30px;
            font-weight: 600;
        }

        .card .contenu-notif {
            font-size: 15px;
            color: #155724;
            font-weight: 900;
        }

        .card .date-notif {
            font-size: 12px;
            color: #6c757d;
            font-weight: 900;
            margin-top: 5px;
            margin-right: 90px;
        }

        .card .status-notif {
            font-size: 12px;
            color: #6c757d;
            font-weight: 900;
            margin-top: 5px;
        }

        .card .supp-btn {
            background-color:rgb(230, 178, 184);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
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

            <!-- Dernière consommation -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Dernière Consommation</h3>
                    <div class="value"><?php echo $consumptionValue; ?> kWh</div>
                    <p><?php echo $consumptionDate; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Dernière Facture</h3>
                    <div class="value"><?php echo $lastFactureMontant; ?> MAD</div>
                    <p><?php echo $lastFacturePeriode; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Moyenne Annuelle</h3>
                    <div class="value">310 kWh</div>
                    <p>Année 2023</p>
                </div>
            </div>

            <!-- Update the factures table -->
            <div class="card">
                <div class="card-header">
                    <h2>Factures Récentes</h2>
                </div>
                <table>
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
                                            class="btn btn-sm btn-info"
                                            target="_blank">
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

            <div class="card">
                <div class="card-header">
                    <h2>Notifications</h2>
                </div>

                <div class="card-body p-0">
                    <?php if (!empty($notifications)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                
                                <tbody>
                                    <?php foreach ($notifications as $notification): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <p class="contenu-notif"><?= htmlspecialchars($notification['contenu']) ?>
                                                    </p>
                                                    <div class="d-flex justify-content-between">
                                                        <small
                                                            class="date-notif"><?= date('d/m/Y H:i', strtotime($notification['date_reponse'])) ?></small>
                                                        <small class="status-notif">Statut:
                                                            <?= $notification['status'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                            <form method="post" action="../../traitement/notificationService.php" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="notificationId" value="<?= $notification['reponse_id'] ?>">
                                                <button type="submit" class="supp-btn">Supprimer Notification</button>
                                            </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted">Aucune nouvelle notification.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
        </div>
    </div>
</body>

</html>