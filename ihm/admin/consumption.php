<?php
session_start();
require_once '../../traitement/consommationService.php';
require_once '../../traitement/StatistiquesService.php';
require_once '../../DB/ClientDAO.php';

// Instancier les services
$consommationService = new ConsommationService();
$statistiquesService = new StatistiquesService();

// Récupérer les métriques dynamiques
$currentMonth = (int)date('m');
$currentYear = (int)date('Y');
$eligibleClientsCount   = $statistiquesService->getEligibleClientsCount();
$saisiesEffectueesCount = $statistiquesService->getSaisiesEffectueesCount($currentMonth, $currentYear);
$anomaliesCount         = $statistiquesService->getAnomaliesCount($currentMonth, $currentYear);
$clientsEnRetardCount   = $statistiquesService->getClientsEnRetardCount($currentMonth, $currentYear);

// Récupérer les anomalies
$anomalies = $consommationService->getAllMonthlyConsumptionsWithAnomaly();
$annualAnomalies = $consommationService->getAnnualConsumptionsWithAnomalies();

// Générer les factures annuelles
$invoicesGenerated = $consommationService->generateInvoicesForAnnualAnomalies();
$successMessage = $invoicesGenerated > 0 ? "$invoicesGenerated nouvelles factures annuelles générées" : '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Saisies - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
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
        #monthly-anomalies-content {
            display: block;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .badge-anomaly {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .control-panel {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
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
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                <a href="clients.php"><i class="fas fa-users"></i> Gestion des clients</a>
                <a href="factures.php"><i class="fas fa-file-invoice"></i> Gestion des factures</a>
                <a href="consumption.php" class="active"><i class="fas fa-bolt"></i> Gestion des saisies</a>
                <a href="claims-frs.php"><i class="fas fa-exclamation-circle"></i> Réclamations</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a>
                <a href="../deconnexion.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success"><?= $successMessage ?></div>
            <?php endif; ?>

            <h1>Gestion des Saisies de Consommation</h1>

            <!-- Dynamic Stats -->
            <div class="dashboard-stats">
                <div class="stat-card primary">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="value"><?= number_format($eligibleClientsCount) ?></div>
                    <div class="label">Clients éligibles</div>
                </div>
                <div class="stat-card success">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <div class="value"><?= number_format($saisiesEffectueesCount) ?></div>
                    <div class="label">Saisies effectuées</div>
                </div>
                <div class="stat-card warning">
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="value"><?= number_format($anomaliesCount) ?></div>
                    <div class="label">Anomalies détectées</div>
                </div>
                <div class="stat-card danger">
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                    <div class="value"><?= number_format($clientsEnRetardCount) ?></div>
                    <div class="label">Clients en retard</div>
                </div>
            </div>

            <!-- Control Panel -->
            

            <!-- Anomalies Section -->
            <div class="card">
                <div class="tab-navigation">
                    <button class="tab-button active" data-target="monthly-anomalies-content">Anomalies Mensuelles</button>
                    <button class="tab-button" data-target="annual-anomalies-content">Anomalies Annuelles</button>
                </div>

                <!-- Monthly Anomalies -->
                <div class="tab-content" id="monthly-anomalies-content">
                    <div class="card-header"><h2>Anomalies Mensuelles Détectées</h2></div>
                    <div class="filters">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Rechercher un client..." id="search-anomaly">
                        </div>
                        <div class="filter-group">
                            <label for="severity-filter">Sévérité:</label>
                            <select id="severity-filter">
                                <option value="">Toutes</option>
                                <option value="high">Haute (>100 kWh)</option>
                                <option value="medium">Moyenne (50-100 kWh)</option>
                                <option value="low">Faible (<50 kWh)</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="status-filter">Statut:</label>
                            <select id="status-filter">
                                <option value="">Tous</option>
                                <option value="pending">En attente</option>
                                <option value="verified">Vérifiée</option>
                                <option value="resolved">Résolue</option>
                            </select>
                        </div>
                    </div>

                    <table class="anomalies-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>ID Compteur</th>
                                <th>Date de saisie</th>
                                <th>Valeur précédente</th>
                                <th>Valeur saisie</th>
                                <th>Écart</th>
                                <th>Statut</th>
                                <th>Corriger</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($anomalies)): ?>
                                <tr><td colspan="8" class="text-center">Aucune anomalie détectée</td></tr>
                            <?php else: ?>
                                <?php foreach ($anomalies as $anomaly): ?>
                                <tr>
                                    <td><?= htmlspecialchars($anomaly->getClientInfo()) ?></td>
                                    <td><?= htmlspecialchars($anomaly->getMeterId()) ?></td>
                                    <td><?= date('d/m/Y', strtotime($anomaly->getEntryDate())) ?></td>
                                    <td><?= number_format($anomaly->getPreviousValue(), 2) ?> kWh</td>
                                    <td><?= number_format($anomaly->getEnteredValue(), 2) ?> kWh</td>
                                    <td>
                                        <span class="badge badge-anomaly">
                                            <?= ($anomaly->getDifference() > 0 ? '+' : '') . number_format($anomaly->getDifference(), 2) ?> kWh
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($anomaly->getStatus()) ?></td>
                                    <td>
                                        <a class="btn btn-secondary btn-sm view-anomaly" 
                                           href="correction.php?id=<?= $anomaly->getAnomalyId() ?>">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Annual Anomalies -->
                <div class="tab-content" id="annual-anomalies-content" style="display: none;">
                    <div class="card-header">
                        <h2>Anomalies Annuelles Détectées</h2>  
                    </div>
                    <div class="filters">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Rechercher un client..." id="search-anomaly">
                        </div>
                        <div class="filter-group">
                            <label for="severity-filter">Sévérité:</label>
                            <select id="severity-filter">
                                <option value="">Toutes</option>
                                <option value="high">Haute (>100 kWh)</option>
                                <option value="medium">Moyenne (50-100 kWh)</option>
                                <option value="low">Faible (<50 kWh)</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="status-filter">Statut:</label>
                            <select id="status-filter">
                                <option value="">Tous</option>
                                <option value="pending">En attente</option>
                                <option value="verified">Vérifiée</option>
                                <option value="resolved">Résolue</option>
                            </select>
                        </div>
                    </div>
                    <table class="table anomalies-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Année</th>
                                <th>Consommation Attendue</th>
                                <th>Consommation Réelle</th>
                                <th>Écart (kWh)</th>
                                <th>Écart (%)</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($annualAnomalies)): ?>
                                <tr><td colspan="7" class="text-center">Aucune anomalie annuelle détectée</td></tr>
                            <?php else: ?>
                                <?php foreach ($annualAnomalies as $anomaly): ?>
                                <?php 
                                    $ecart = $anomaly->getEcart();
                                    $ecartAbs = abs($ecart);
                                    $ecartPct = $anomaly->getConsommationReelle() 
                                        ? number_format(($ecartAbs / $anomaly->getConsommationReelle()) * 100, 2)
                                        : 0;
                                    $ecartClass = $ecartAbs > 200 ? 'badge-anomaly' : ($ecartAbs > 100 ? 'badge-warning' : '');
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($anomaly->getClientName()) ?></td>
                                    <td><?= $anomaly->getAnnee() ?></td>
                                    <td><?= number_format($anomaly->getConsommationAttendue(), 2) ?> kWh</td>
                                    <td><?= number_format($anomaly->getConsommationReelle(), 2) ?> kWh</td>
                                    <td>
                                        <span class="badge <?= $ecartClass ?>">
                                            <?= number_format($ecart, 2) ?> kWh
                                        </span>
                                    </td>
                                    <td><?= $ecartPct ?>%</td>
                                    <td><?= htmlspecialchars($anomaly->getStatut()) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Dernières Saisies Section -->
            <div class="card">
                <div class="card-header"><h2>Dernières Saisies</h2></div>
                <!-- ... (keep existing entries section) ... -->
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div id="config-period-modal" class="modal"><!-- ... --></div>
    <div id="view-anomaly-modal" class="modal"><!-- ... --></div>

    <script src="../../assets/js/main.js"></script>
    <script src="js/consumption.js"></script>
    <script>
        // Tab Navigation
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.tab-button');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    document.querySelectorAll('.tab-content').forEach(content => 
                        content.style.display = 'none');
                    document.querySelectorAll('.tab-button').forEach(btn => 
                        btn.classList.remove('active'));
                    
                    const target = document.getElementById(tab.dataset.target);
                    target.style.display = 'block';
                    tab.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>