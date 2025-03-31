<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../../DB/ClientDAO.php';

$clientDAO = new ClientDAO();
$client = $clientDAO->getClientByUserId($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .profile-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 30px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-right: 20px;
        }

        .profile-details h2 {
            margin: 0 0 5px 0;
            color: var(--primary-color);
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .info-label {
            width: 30%;
            font-weight: bold;
            color: var(--dark-color);
        }

        .info-value {
            width: 70%;
        }

        .edit-btn {
            color: var(--secondary-color);
            cursor: pointer;
            margin-left: 10px;
        }

        .chart-container {
            height: 400px;
            margin-top: 20px;
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
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="consumption.php">
                    <i class="fas fa-bolt"></i> Saisie Consommation
                </a>
                <a href="claims.php">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="profile.php" class="active">
                    <i class="fas fa-user"></i> Mon Profil
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Mon Profil</h1>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-details">
                        <h2><?= htmlspecialchars($client ? $client->getFullName() : 'Profil Inconnu') ?></h2>
                    </div>
                </div>
                <div class="profile-info">
                    <div class="info-row">
                        <div class="info-label">Numéro de client</div>
                        <div class="info-value"><?= $client ? $client->getClientId() : '-' ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Client depuis</div>
                        <div class="info-value">
                            <?= $client && $client->getCreatedAt() ? date('d/m/Y', strtotime($client->getCreatedAt())) : '-' ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Adresse</div>
                        <div class="info-value"><?= $client ? htmlspecialchars($client->getAddress()) : '-' ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value"><?= $client ? htmlspecialchars($client->getPhone()) : '-' ?></div>
                    </div>
                    <!-- Vous pouvez ajouter d'autres informations dynamiques ici -->
                </div>
            </div>
        </div>
    </div>
</body>

</html>