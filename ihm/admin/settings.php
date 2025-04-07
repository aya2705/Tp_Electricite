<?php
require_once '../../DB/TarificationDAO.php';
require_once '../../DB/PeriodeSaisieDAO.php';

$tarificationDAO = new TarificationDAO();
$tarification = $tarificationDAO->getTarification();

$periodeSaisieDAO = new PeriodeSaisieDAO();
$periodeSaisie = $periodeSaisieDAO->getPeriodeSaisie();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_tarification'])) {
        $tranche1 = $_POST['tranche1'];
        $tranche2 = $_POST['tranche2'];
        $tranche3 = $_POST['tranche3'];
        $tva = $_POST['tva'];

        $tarificationDAO->updateTarification($tranche1, $tranche2, $tranche3, $tva);
        header("Location: settings.php?success=1");
        exit;
    }

    if (isset($_POST['update_periode'])) {
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $active = isset($_POST['active']) ? 1 : 0;

        $periodeSaisieDAO->updatePeriodeSaisie($date_debut, $date_fin, $active);
        header("Location: settings.php?periode_success=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .settings-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 30px;
        }

        .settings-section h2 {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .settings-group {
            margin-bottom: 30px;
        }

        .settings-group h3 {
            margin-bottom: 15px;
            color: var(--secondary-color);
        }

        .settings-description {
            margin-bottom: 15px;
            color: #666;
        }

        .settings-form {
            max-width: 800px;
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

        .setting-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5f5f5;
        }

        .setting-row:last-child {
            border-bottom: none;
        }

        .setting-label {
            flex: 1;
        }

        .setting-control {
            flex: 2;
        }

        .template-list {
            margin-top: 20px;
        }

        .template-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .template-item h4 {
            margin: 0 0 10px 0;
            display: flex;
            justify-content: space-between;
        }

        .template-actions {
            display: flex;
            gap: 10px;
        }

        .template-actions button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .template-actions .edit-btn {
            color: var(--secondary-color);
        }

        .template-actions .delete-btn {
            color: var(--danger-color);
        }

        .status-active {
            color: var(--success-color);
            font-weight: bold;
        }

        .status-inactive {
            color: var(--danger-color);
            font-weight: bold;
        }

        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .status-active {
            background-color: var(--success-color);
        }

        .status-inactive {
            background-color: var(--danger-color);
        }

        .settings-form input[type="date"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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
                <a href="consumption.php">
                    <i class="fas fa-bolt"></i> Gestion des saisies
                </a>
                <a href="claims-frs.php">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="settings.php" class="active">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Paramètres</h1>

            <div class="settings-section">
                <h2>Tarification</h2>

                <div class="settings-group">
                    <p class="settings-description">Configurez les tranches de tarification</p>

                    <form class="settings-form" method="POST" action="">
                        <div class="setting-row">
                            <div class="setting-label">
                                <label for="tranche1">Tranche 1 (0-100 kWh)</label>
                            </div>
                            <div class="setting-control">
                                <div style="display: flex; align-items: center;">
                                    <input type="number" id="tranche1" name="tranche1"
                                        value="<?= htmlspecialchars($tarification['tranche1'] ?? 0.82) ?>" step="0.01"
                                        required class="form-control" style="width: 100px;">
                                    <span style="margin: 0 10px;">DH/kWh</span>
                                </div>
                            </div>
                        </div>

                        <div class="setting-row">
                            <div class="setting-label">
                                <label for="tranche2">Tranche 2 (101-150 kWh)</label>
                            </div>
                            <div class="setting-control">
                                <div style="display: flex; align-items: center;">
                                    <input type="number" id="tranche2" name="tranche2"
                                        value="<?= htmlspecialchars($tarification['tranche2'] ?? 0.92) ?>" step="0.01"
                                        required class="form-control" style="width: 100px;">
                                    <span style="margin: 0 10px;">DH/kWh</span>
                                </div>
                            </div>
                        </div>

                        <div class="setting-row">
                            <div class="setting-label">
                                <label for="tranche3">Tranche 3 (> 150 kWh)</label>
                            </div>
                            <div class="setting-control">
                                <div style="display: flex; align-items: center;">
                                    <input type="number" id="tranche3" name="tranche3"
                                        value="<?= htmlspecialchars($tarification['tranche3'] ?? 1.1) ?>" step="0.01"
                                        required class="form-control" style="width: 100px;">
                                    <span style="margin: 0 10px;">DH/kWh</span>
                                </div>
                            </div>
                        </div>

                        <div class="setting-row">
                            <div class="setting-label">
                                <label for="tva">TVA</label>
                            </div>
                            <div class="setting-control">
                                <div style="display: flex; align-items: center;">
                                    <input type="number" id="tva" name="tva"
                                        value="<?= htmlspecialchars($tarification['tva'] ?? 18) ?>" step="0.01" required
                                        class="form-control" style="width: 100px;">
                                    <span style="margin: 0 10px;">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-3">
                            <button type="submit" name="update_tarification" class="btn btn-primary">Enregistrer les
                                paramètres</button>
                            <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="control-panel">
                <h2>Contrôle de la période de saisie</h2>
                <div class="period-status">
                    <div class="status-indicator <?= $periodeSaisie['active'] ? 'status-active' : 'status-inactive' ?>">
                    </div>
                    <div>
                        <strong>Période de saisie :</strong>
                        <?php if ($periodeSaisie['active']): ?>
                            ACTIVE (du <?= htmlspecialchars($periodeSaisie['date_debut']) ?> au
                            <?= htmlspecialchars($periodeSaisie['date_fin']) ?>)
                        <?php else: ?>
                            INACTIVE (prévue du <?= htmlspecialchars($periodeSaisie['date_debut']) ?> au
                            <?= htmlspecialchars($periodeSaisie['date_fin']) ?>)
                        <?php endif; ?>
                    </div>
                </div>

                <form method="POST" action="" class="settings-form mt-3">
                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="date_debut">Date de début</label>
                        </div>
                        <div class="setting-control">
                            <input type="date" id="date_debut" name="date_debut"
                                value="<?= htmlspecialchars($periodeSaisie['date_debut']) ?>" required>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="date_fin">Date de fin</label>
                        </div>
                        <div class="setting-control">
                            <input type="date" id="date_fin" name="date_fin"
                                value="<?= htmlspecialchars($periodeSaisie['date_fin']) ?>" required>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="active">Activer la période</label>
                        </div>
                        <div class="setting-control">
                            <label class="toggle-switch">
                                <input type="checkbox" id="active" name="active" <?= $periodeSaisie['active'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions mt-3">
                        <button type="submit" name="update_periode" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>