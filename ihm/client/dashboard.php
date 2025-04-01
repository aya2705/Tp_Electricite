<?php
// Début du fichier dashboard.php - REMPLACEZ le code existant
session_start();

// FORCER un utilisateur spécifique (à utiliser seulement pour le test)
$_SESSION['client_id'] = 1; // ID du client que vous voulez tester
$_SESSION['user_id'] = 1;
$_SESSION['email'] = 'client1@test.com';
$_SESSION['role'] = 'client';

// Debug - Affiche les infos session (à enlever en production)
echo "<div style='background:#f0f0f0; padding:10px; margin-bottom:20px;'>";
echo "<strong>Mode TEST activé :</strong> Utilisateur forcé (ID: ".$_SESSION['client_id'].")";
echo "</div>";

// Connexion DB et récupération des factures (votre code existant)
require_once __DIR__.'/../../DB/connexion.php';
require_once __DIR__.'/../../models/facture.php';
require_once __DIR__.'/../../DB/factureDAO.php';

try {
    $db = creerConnexion();
    $factureDAO = new FactureDAO($db);
    $factures = $factureDAO->getByClient($_SESSION['client_id'], 3);
} catch (PDOException $e) {
    die("Erreur DB: ".$e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Client - Gestion des Factures</title>
    <link rel="stylesheet" href="../assets/css/main.css">
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
                <a href="dashboard.html" class="active">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="consumption.html">
                    <i class="fas fa-bolt"></i> Saisie Consommation
                </a>
                <a href="claims.html">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="profile.html">
                    <i class="fas fa-user"></i> Mon Profil
                </a>
                <a href="../index.html" class="logout">
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
            
            <!-- <div class="card">
                <div class="card-header">
                    <h2>Consommation Mensuelle</h2>
                </div>
                <div class="consumption-chart" id="consumption-chart"></div>
                <div class="text-center mt-3">
                    <button id="input-consumption-btn" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Saisir ma consommation
                    </button>
                </div>
            </div> -->
            
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
    <?php if (!empty($factures)): ?>
        <?php foreach ($factures as $facture): ?>
        <tr>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($facture->getDateEmission()))) ?></td>
            <td>FACT-<?= htmlspecialchars(date('Y-m', strtotime($facture->getPeriode()))) ?></td>
            <td><?= htmlspecialchars($facture->getConsommation()) ?> kWh</td>
            <td><?= htmlspecialchars(number_format($facture->getMontant(), 0, ',', ' ')) ?> XOF</td>
            <td>
                <span class="status-badge <?= $facture->getStatutPaiement() === 'payée' ? 'status-paid' : 'status-unpaid' ?>">
                    <?= htmlspecialchars($facture->getStatutPaiement()) ?>
                </span>
            </td>
            <td>
    <a href="../../traitement/generate_pdf.php?id=<?= $facture->getFactureId() ?>" class="btn-pdf" download>
        <i class="fas fa-file-pdf"></i> PDF
    </a>
</td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">Aucune facture disponible</td>
        </tr>
    <?php endif; ?>
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
    
    <script src="../assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>
