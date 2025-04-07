<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Traitement de la récupération des réclamations
require_once '../../DB/ReclamationDAO.php';
require_once '../../DB/connexion.php';

// Récupérer toutes les réclamations
$reclamations = ReclamationDAO::getAllReclamations();
session_start(); // Démarrer la session pour récupérer la valeur
$reclamationId = $_SESSION['reclamation_id'] ?? null;

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réclamations - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main-frs.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <a href="claims-frs.php" class="active">
                    <i class="fas fa-exclamation-circle" class="active"></i> Réclamations
                </a>
                <a href="settings.php" >
                    <i class="fas fa-cog"></i> Paramètres
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Gestion des Réclamations</h1>

            <div class="filters">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher une réclamation..." id="search-claim">
                </div>
                <div class="filter-group">
                    <label for="status-filter">Statut:</label>
                    <select id="status-filter">
                        <option value="">Tous</option>
                        <option value="pending">En attente</option>
                        <option value="processing">En traitement</option>
                        <option value="resolved">Résolue</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="type-filter">Type:</label>
                    <select id="type-filter">
                        <option value="">Tous</option>
                        <option value="fuite_externe">Fuite externe</option>
                        <option value="fuite_interne">Fuite interne</option>
                        <option value="facture">Facture</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
            </div>


            <div class="claims-list" id="claims-list">
                <?php foreach ($reclamations as $reclamation): ?>
                    <div class="claim-item">
                        <div class="claim-icon">
                            <?php
                            // Affichage d'une icône en fonction du type de réclamation
                            switch ($reclamation->getType()) {
                                case 'fuite_externe':
                                    echo '<i class="fas fa-tint"></i>';
                                    break;
                                case 'facture':
                                    echo '<i class="fas fa-file-invoice"></i>';
                                    break;
                                default:
                                    echo '<i class="fas fa-exclamation-circle"></i>';
                                    break;
                            }
                            ?>
                        </div>
                        <div class="claim-content">
                            <h3><?php echo htmlspecialchars($reclamation->getType()); ?></h3>
                            <p><?php echo htmlspecialchars($reclamation->getDescription()); ?></p>
                            <div class="claim-meta">
                                <div>
                                    <span class="claim-type"><?php echo htmlspecialchars($reclamation->getType()); ?></span>
                                    <span>Référence: #REF-<?php echo $reclamation->getReclamationId(); ?></span>
                                </div>
                                <span>Soumise le
                                    <?php echo date("d/m/Y", strtotime($reclamation->getDateCreation())); ?></span>
                            </div>
                        </div>
                        <div class="claim-actions">
                            <button class="action-process" data-id="REF-<?php echo $reclamation->getReclamationId(); ?>"
                                title="Traiter">
                                <i class="fas fa-tools"></i>
                            </button>
                        </div>
                        <span class="claim-status <?php echo 'status-' . $reclamation->getStatut(); ?>">
                            <?php echo ucfirst($reclamation->getStatut()); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            
        </div>
    </div>

    <!-- Modal pour traiter une réclamation -->
    <div id="process-claim-modal"
        class="modal fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="modal-content bg-white rounded-lg shadow-lg w-11/12 max-w-4xl p-6">
            <div class="modal-header flex justify-between items-center border-b pb-3">
                <h2 class="text-xl font-semibold text-gray-700">Traiter la réclamation</h2>
                <span class="close text-gray-500 text-2xl cursor-pointer">&times;</span>
            </div>
            <div class="modal-body mt-4">
                <div class="claim-detail-container grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="claim-detail-info">
                        <div class="client-info bg-gray-100 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-700">Information Client</h3>
                            <p><strong>Nom:</strong> <span id="client-name">{{ clientName }}</span></p>
                            <p><strong>ID Client:</strong> <span id="client-id">{{ clientId }}</span></p>
                            <p><strong>Adresse:</strong> <span id="client-address">{{ clientAddress }}</span></p>
                            <p><strong>Email:</strong> <span id="client-email">{{ clientEmail }}</span></p>
                            <p><strong>Téléphone:</strong> <span id="client-phone">{{ clientPhone }}</span></p>
                        </div>
                        <div class="claim-detail bg-gray-100 p-4 rounded-lg mt-4">
                            <h3 class="text-lg font-semibold text-gray-700">Détails de la réclamation</h3>
                            <p><strong>Référence:</strong> <span id="claim-ref">{{ claimRef }}</span></p>
                            <p><strong>Type:</strong> <span id="claim-type">{{ claimType }}</span></p>
                            <p><strong>Date de soumission:</strong> <span id="claim-date">{{ claimDate }}</span></p>
                            <p><strong>Description:</strong></p>
                            <div class="claim-description p-2 bg-white rounded border border-gray-300">{{
                                claimDescription }}</div>
                            <p class="mt-3"><strong>Pièces jointes:</strong></p>
                            <div class="attachments grid grid-cols-2 gap-2">
                                {{#each piecesJointes}}
                                <div
                                    class="attachment flex items-center space-x-2 bg-white p-2 rounded border border-gray-300">
                                    <i class="fas fa-paperclip text-gray-500"></i>
                                    <a href="../../uploads/claims/{{ this }}" target="_blank"
                                        class="text-blue-500 hover:underline">{{ this }}</a>
                                </div>
                                {{/each}}
                            </div>
                        </div>
                    </div>
                    <div class="claim-response bg-gray-100 p-4 rounded-lg">
                        <form id="response-form" method="POST" action="../../traitement/reclamationService.php">
                            <input type="hidden" name="action" value="respond">
                            <input type="hidden" id="reclamation_id" name="reclamation_id"
                                value="<?php echo htmlspecialchars($reclamationId); ?>">

                            <div class="form-group">
                                <label for="response-text">Réponse:</label>
                                <textarea id="response-text" rows="10" name="response_text" class="form-control"
                                    required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Statut:</label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="claim_status" value="en_traitement" checked> En
                                        traitement
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="claim_status" value="résolue"> Résolue
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="claim_status" value="refusée"> Refusée
                                    </label>


                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>


      

    </script>
    <script src="../../assets/js/claims-frs.js"></script>
</body>

</html>