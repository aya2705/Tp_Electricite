<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
  require_once "../../DB/ReclamationDAO.php";
 session_start();

 // Vérifier si les réclamations sont stockées dans la session
if (isset($_SESSION['reclamations']) && is_array($_SESSION['reclamations'])) {
    $reclamations = $_SESSION['reclamations'];
} else {
    // Si aucune réclamation n'est trouvée, récupérer les réclamations à partir de la base de données
    $client_id = $_SESSION['client_id'] ?? 1;
    $reclamations = ReclamationDAO::getReclamationsByClientId($client_id);
}
 ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réclamations - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Espace Client</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                <a href="consumption.php"><i class="fas fa-bolt"></i> Saisie Consommation</a>
                <a href="claims.php" class="active"><i class="fas fa-exclamation-circle"></i> Réclamations</a>
                <a href="profile.php"><i class="fas fa-user"></i> Mon Profil</a>
                <a href="../deconnexion.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>

        <!-- Main Content -->
        <!-- Main Content -->
        <div class="main-content">
            <h1>Mes Réclamations</h1>

            <!-- Formulaire de nouvelle réclamation (placé en haut) -->
            <div class="card">
                <div class="card-header">
                    <h2>Nouvelle Réclamation</h2>
                </div>

                <form id="new-claim-form" action="../../traitement/ReclamationService.php" method="POST"
                    enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="hidden" name="action" value="add">
                        <label for="claim-type">Type de réclamation</label>
                        <select id="claim-type" name="claim_type" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="fuite_externe">Fuite externe</option>
                            <option value="fuite_interne">Fuite interne</option>
                            <option value="facture">Facture</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="claim-description">Description détaillée</label>
                        <textarea id="claim-description" name="description" rows="5" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="claim-attachments">Pièces jointes</label>
                        <input type="file" id="claim-attachments" name="attachments[]" multiple accept="image/*,.pdf">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Soumettre la réclamation</button>
                    </div>
                </form>
            </div>

            <!-- Liste des réclamations -->
            <div class="card">
        <div class="card-header">
            <h2>Réclamations en cours</h2>
        </div>

        <div class="claims-list">
            <!-- Affichage dynamique des réclamations -->
            <?php

            foreach ($reclamations as $claim) {
                echo '<div class="claim-item">
                        <div class="claim-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="claim-content">
                            <h3>' . htmlspecialchars($claim['type']) . '</h3>
                            <p>' . nl2br(htmlspecialchars($claim['description'])) . '</p>
                            <div class="claim-meta">
                                <span class="claim-type">' . htmlspecialchars($claim['type']) . '</span>
                                <span>Référence: #' . htmlspecialchars($claim['reclamation_id']) . '</span>
                                <span>Soumise le ' . htmlspecialchars($claim['date_creation']) . '</span>
                            </div>
                        </div>
                        <span class="claim-status status-' . ($claim['statut'] == "résolue" ? "resolved" : "pending") . '">' . htmlspecialchars($claim['statut']) . '</span>
                    </div>';
            }
            ?>
        </div>
    </div>
</div>

</body>

</html>