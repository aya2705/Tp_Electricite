<?php
session_start();
require_once '../../traitement/consommationService.php';
$consommationService = new ConsommationService();
$anomalies = $consommationService->getAllMonthlyConsumptionsWithAnomaly();
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

        .stat-card {
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .stat-card .icon {
            font-size: 36px;
            margin-bottom: 15px;
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

        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .stat-card .label {
            color: #666;
        }

        .filters {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .search-container {
            flex-grow: 1;
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }

        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .control-panel {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .control-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .period-status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
        }

        .status-active {
            background-color: var(--success-color);
        }

        .status-inactive {
            background-color: var(--danger-color);
        }

        .anomalies-table {
            margin-top: 20px;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: var(--success-color);
        }

        input:focus+.toggle-slider {
            box-shadow: 0 0 1px var(--success-color);
        }

        input:checked+.toggle-slider:before {
            transform: translateX(30px);
        }

        .badge-anomaly {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-normal {
            background-color: #d4edda;
            color: #155724;
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
                <a href="factures.php">
                    <i class="fas fa-file-invoice"></i> Gestion des factures
                </a>
                <a href="claims-frs.php">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="consumption.php" class="active">
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
            <h1>Gestion des Saisies de Consommation</h1>

            <div class="dashboard-stats">
                <div class="stat-card primary">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="value">1,245</div>
                    <div class="label">Clients éligibles</div>
                </div>
                <div class="stat-card success">
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="value">846</div>
                    <div class="label">Saisies effectuées</div>
                </div>
                <div class="stat-card warning">
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="value">24</div>
                    <div class="label">Anomalies détectées</div>
                </div>
                <div class="stat-card danger">
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="value">375</div>
                    <div class="label">Clients en retard</div>
                </div>
            </div>

            <div class="control-panel">
                <h2>Contrôle de la période de saisie</h2>
                <div class="period-status">
                    <div class="status-indicator status-active"></div>
                    <div>
                        <strong>Période de saisie:</strong> ACTIVE jusqu'au 30/11/2023
                    </div>
                </div>
                <div class="control-actions mt-3">
                    <label class="toggle-switch">
                        <input type="checkbox" id="period-toggle" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span>Activer/Désactiver la période de saisie</span>
                    <button class="btn btn-secondary" id="config-period-btn">
                        <i class="fas fa-cog"></i> Configurer les dates
                    </button>

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Anomalies détectées</h2>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($anomalies)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Aucune anomalie détectée</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($anomalies as $anomaly): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($anomaly->getClientInfo()); ?></td>
                                    <td><?php echo htmlspecialchars($anomaly->getMeterId()); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($anomaly->getEntryDate())); ?></td>
                                    <td><?php echo number_format($anomaly->getPreviousValue(), 2) . " kWh"; ?></td>
                                    <td><?php echo number_format($anomaly->getEnteredValue(), 2) . " kWh"; ?></td>
                                    <td>
                                        <span class="badge badge-anomaly">
                                            <?php echo ($anomaly->getDifference() > 0 ? '+' : '') . number_format($anomaly->getDifference(), 2) . " kWh"; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($anomaly->getStatus()); ?></td>
                                    <td>
                                        <button class="btn btn-secondary btn-sm view-anomaly" data-id="<?php echo htmlspecialchars($anomaly->getAnomalyId()); ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm resolve-anomaly" data-id="<?php echo htmlspecialchars($anomaly->getAnomalyId()); ?>">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Dernières Saisies</h2>
                </div>
                <div class="filters">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher un client..." id="search-consumption">
                    </div>
                    <div class="filter-group">
                        <label for="date-filter">Date:</label>
                        <select id="date-filter">
                            <option value="">Toutes</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="yesterday">Hier</option>
                            <option value="week">Cette semaine</option>
                        </select>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>ID Compteur</th>
                            <th>Date de saisie</th>
                            <th>Valeur</th>
                            <th>Consommation</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Jean Dupont (#CL-2020-1245)</td>
                            <td>E-56789-2020</td>
                            <td>22/11/2023</td>
                            <td>15,820 kWh</td>
                            <td><span class="badge badge-normal">325 kWh</span></td>
                            <td>Validée</td>
                            <td>
                                <button class="btn btn-secondary btn-sm view-consumption" data-id="CN-2023-001">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Sophie Leroy (#CL-2019-0789)</td>
                            <td>E-34567-2019</td>
                            <td>21/11/2023</td>
                            <td>9,450 kWh</td>
                            <td><span class="badge badge-normal">310 kWh</span></td>
                            <td>Validée</td>
                            <td>
                                <button class="btn btn-secondary btn-sm view-consumption" data-id="CN-2023-002">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Paul Dubois (#CL-2018-0456)</td>
                            <td>E-23456-2018</td>
                            <td>21/11/2023</td>
                            <td>18,670 kWh</td>
                            <td><span class="badge badge-normal">350 kWh</span></td>
                            <td>Validée</td>
                            <td>
                                <button class="btn btn-secondary btn-sm view-consumption" data-id="CN-2023-003">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center mt-3">
                    <button class="btn btn-secondary" id="export-btn">
                        <i class="fas fa-file-export"></i> Exporter en Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour configurer la période -->
    <div id="config-period-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Configurer la période de saisie</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="period-form">
                    <div class="form-group">
                        <label for="month-select">Mois concerné:</label>
                        <select id="month-select" class="form-control">
                            <option value="11">Novembre 2023</option>
                            <option value="12">Décembre 2023</option>
                            <option value="1">Janvier 2024</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start-date">Date de début:</label>
                        <input type="date" id="start-date" class="form-control" value="2023-11-18">
                    </div>
                    <div class="form-group">
                        <label for="end-date">Date de fin:</label>
                        <input type="date" id="end-date" class="form-control" value="2023-11-30">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour voir une anomalie -->
    <div id="view-anomaly-modal" class="modal">
        <div class="modal-content" style="width: 90%; max-width: 900px;">
            <div class="modal-header">
                <h2>Détails de l'anomalie</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h3>Informations client</h3>
                        <p><strong>Nom:</strong> Marie Martin</p>
                        <p><strong>ID Client:</strong> #CL-2021-0587</p>
                        <p><strong>Adresse:</strong> 8 Rue du Commerce, 75015 Paris</p>
                        <p><strong>ID Compteur:</strong> E-12345-2021</p>
                        <p><strong>Consommation moyenne:</strong> 300 kWh/mois</p>

                        <h3 class="mt-3">Détails de l'anomalie</h3>
                        <p><strong>Relevé précédent:</strong> 12,450 kWh (20/10/2023)</p>
                        <p><strong>Relevé saisi:</strong> 12,970 kWh (18/11/2023)</p>
                        <p><strong>Consommation calculée:</strong> 520 kWh</p>
                        <p><strong>Écart par rapport à la moyenne:</strong> +120 kWh (40%)</p>

                    </div>
                    <div>
                        <h3>Photos du compteur</h3>
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <div>
                                <p><strong>Photo actuelle (18/11/2023)</strong></p>
                                <img src="../assets/images/meter-sample.jpg" alt="Photo du compteur actuel" style="width: 100%; max-width: 400px; border-radius: 4px;">
                            </div>
                            <div>
                                <p><strong>Photo précédente (20/10/2023)</strong></p>
                                <img src="../assets/images/meter-sample-prev.jpg" alt="Photo du compteur précédent" style="width: 100%; max-width: 400px; border-radius: 4px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script src="js/consumption.js"></script>
</body>

</html>