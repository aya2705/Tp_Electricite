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

$clientId = $_SESSION['client_id'] ?? 0;

// Initialize services
$factureMensuelleService = new FactureMensuelleService();
$consommationService = new ConsommationService();
$notificationService = new NotificationService();

// Get client data
$factures = $factureMensuelleService->getAllFacturesByClient($clientId);
$lastFacture = $factureMensuelleService->getLastFactureMensuelleByClient($clientId);
$lastConsumption = $consommationService->getLastMonthlyConsumption($clientId);
$notifications = $notificationService->getClientNotifications($clientId);

// Format data for display
$consumptionDate = !empty($lastConsumption) ? date('F Y', strtotime($lastConsumption->getCreatedAt())) : 'N/A';
$consumptionValue = !empty($lastConsumption) ? $lastConsumption->getKw() : 0;
$lastFactureMontant = !empty($lastFacture) ? number_format($lastFacture['montant'], 2) : '0.00';
$lastFacturePeriode = !empty($lastFacture) ? date('F Y', strtotime($lastFacture['date_emission'])) : 'N/A';

// Modification ici : récupération de la moyenne du montant des factures mensuelles
$year = date('Y');
$avgMontant = $factureMensuelleService->getAnnualAverageMontant($clientId, $year);
$formattedAvg = number_format($avgMontant, 2) . " MAD";
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
                    <div class="value"><?php echo $formattedAvg; ?></div>
                    <p>Année <?php echo $year; ?></p>
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
                        <div id="notifications-container" class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    <?php foreach ($notifications as $notification): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <p class="contenu-notif"><?= htmlspecialchars($notification['content']) ?></p>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="date-notif"><?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?></small>
                                                        <small class="status-notif">Statut: <?= $notification['status'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <form method="post" action="../../traitement/notificationService.php">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="notificationId" value="<?= $notification['notification_id'] ?>">
                                                    <button type="submit" class="supp-btn"><i class="fa-solid fa-trash"></i></button>
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