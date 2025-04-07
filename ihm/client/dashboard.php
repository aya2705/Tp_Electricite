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
require_once '../../traitement/FactureAnnuelleService.php';

$clientId = $_SESSION['client_id'];

// Initialize services
$factureMensuelleService = new FactureMensuelleService();
$factureAnnuelleService = new FactureAnnuelleService();
$consommationService = new ConsommationService();

// Get client data
$factures = $factureMensuelleService->getAllFacturesByClient($clientId);
$facturesAnnuelles = $factureAnnuelleService->getFacturesAnnuellesByClient($clientId);
$lastFacture = $factureMensuelleService->getLastFactureMensuelleByClient($clientId);
$lastConsumption = $consommationService->getLastMonthlyConsumption($clientId);

// Format data for display
$consumptionDate = !empty($lastConsumption) ? date('F Y', strtotime($lastConsumption->getCreatedAt())) : 'N/A';
$consumptionValue = !empty($lastConsumption) ? $lastConsumption->getKw() : 0;
$lastFactureMontant = !empty($lastFacture) ? number_format($lastFacture['montant'], 2) : '0.00';
$lastFacturePeriode = !empty($lastFacture) ? date('F Y', strtotime($lastFacture['date_emission'])) : 'N/A';

// Utiliser la classe NotificationDAO
$notificationDAO = new NotificationDAO();
$notifications = $notificationDAO->getNotifications($clientId);
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
        .tab-navigation {
            display: flex;
            margin-bottom: 20px;
            gap: 10px;
        }

        .tab-button {
            padding: 8px 16px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .tab-content {
            display: none;
        }

        #monthly-invoices-content {
            display: block;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 4px;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 4px;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 4px;
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

            <!-- Factures Section with Tabs -->
            <div class="card">
                <div class="card-header">
                    <h2>Mes Factures</h2>
                    <div class="tab-navigation">
                        <button class="tab-button active" data-target="monthly-invoices-content">Factures Mensuelles</button>
                        <button class="tab-button" data-target="annual-invoices-content">Factures Annuelles</button>
                    </div>
                </div>

                <!-- Monthly Invoices Tab -->
                <div class="tab-content" id="monthly-invoices-content">
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
                                    <td colspan="6" class="text-center">Aucune facture mensuelle disponible</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Annual Invoices Tab -->
                <div class="tab-content" id="annual-invoices-content" style="display: none;">
    <table>
        <thead>
            <tr>
                <th>Année</th>
                <th>N° Facture</th>
                <th>Consommation Attendue</th>
                <th>Consommation Réelle</th>
                <th>Écart</th>
                <th>Écart Facturé</th>
                <th>Montant</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($facturesAnnuelles)): ?>
                <?php foreach ($facturesAnnuelles as $facture): ?>
                    <?php 
                        $ecartAbs = abs($facture['ecart']); 
                    ?>
                    <tr>
                        <td><?php echo $facture['annee']; ?></td>
                        <td>FA-<?php echo str_pad($facture['facture_annuelle_id'], 6, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo number_format($facture['consommation_attendue'], 0); ?> kWh</td>
                        <td><?php echo number_format($facture['consommation_reelle'], 0); ?> kWh</td>
                        <td><?php echo number_format($facture['ecart'], 0); ?> kWh</td>
                        <td><?php echo number_format($ecartAbs, 0); ?> kWh</td>
                        <td><?php echo number_format($facture['montant_total'], 2); ?> MAD</td>
                        <td class="actions">
                            <a href="../../traitement/generate_annual_pdf.php?id=<?php echo $facture['facture_annuelle_id']; ?>"
                                class="btn btn-sm btn-info" target="_blank">
                                <i class="fas fa-download"></i> Télécharger
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Aucune facture annuelle disponible</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
            </div>

            <!-- Notifications Section -->
            <div class="card">
                <div class="card-header">
                    <h2 class="notif">Notifications</h2>
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