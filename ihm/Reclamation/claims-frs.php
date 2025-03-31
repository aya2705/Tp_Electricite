<?php
// Traitement de la récupération des réclamations
require_once '../../DB/models/Reclamation.php';

$reclamations = Reclamation::getAllReclamations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réclamations - Gestion des Factures</title>
    <link rel="stylesheet" href="css/main-frs.css">
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
                <a href="dashboard.html">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="clients.html">
                    <i class="fas fa-users"></i> Gestion des clients
                </a>
                <a href="claims.html" class="active">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="consumption.html">
                    <i class="fas fa-bolt"></i> Gestion des saisies
                </a>
                <a href="settings.html">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
                <a href="../index.html" class="logout">
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
                <button class="btn btn-primary" id="refresh-claims-btn">
                    <i class="fas fa-sync-alt"></i> Actualiser
                </button>
            </div>
            
            <div class="claims-list" id="claims-list">
                <?php foreach ($reclamations as $reclamation): ?>
                    <div class="claim-item">
                        <div class="claim-icon">
                            <?php
                            // Affichage d'une icône en fonction du type de réclamation
                            switch ($reclamation['type']) {
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
                            <h3><?php echo htmlspecialchars($reclamation['type']); ?></h3>
                            <p><?php echo htmlspecialchars($reclamation['description']); ?></p>
                            <div class="claim-meta">
                                <div>
                                    <span class="claim-type"><?php echo htmlspecialchars($reclamation['type']); ?></span>
                                    <span>Référence: #REF-<?php echo $reclamation['reclamation_id']; ?></span>
                                </div>
                                <span>Soumise le <?php echo date("d/m/Y", strtotime($reclamation['date_creation'])); ?></span>
                            </div>
                        </div>
                        <div class="claim-actions">
                            <button class="action-process" data-id="REF-<?php echo $reclamation['reclamation_id']; ?>" title="Traiter">
                                <i class="fas fa-tools"></i>
                            </button>
                            <button class="action-resolve" data-id="REF-<?php echo $reclamation['reclamation_id']; ?>" title="Marquer comme résolu">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                        <span class="claim-status <?php echo 'status-' . $reclamation['statut']; ?>">
                            <?php echo ucfirst($reclamation['statut']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            
            <div class="pagination">
                <a href="#" class="active">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#">&raquo;</a>
            </div>
        </div>
    </div>
    
    <!-- Modal pour traiter une réclamation -->
    <div id="process-claim-modal" class="modal">
    <div class="modal-content" style="width: 90%; max-width: 1000px;">
        <div class="modal-header">
            <h2>Traiter la réclamation</h2>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="claim-detail-container">
                <div class="claim-detail-info">
                    <!-- Informations sur le client -->
                    <div class="client-info">
                        <h3>Information Client</h3>
                        <p><strong>Nom:</strong> <span id="client-name"><?php echo htmlspecialchars($clientName ?? ''); ?></span></p>
                        <p><strong>ID Client:</strong> <span id="client-id"><?php echo htmlspecialchars($clientId ?? ''); ?></span></p>
                        <p><strong>Adresse:</strong> <span id="client-address"><?php echo htmlspecialchars($clientAddress ?? ''); ?></span></p>
                        <p><strong>Email:</strong> <span id="client-email"><?php echo htmlspecialchars($clientEmail ?? ''); ?></span></p>
                        <p><strong>Téléphone:</strong> <span id="client-phone"><?php echo htmlspecialchars($clientPhone ?? ''); ?></span></p>
                    </div>

                    <!-- Détails de la réclamation -->
                    <div class="claim-detail">
                        <h3>Détails de la réclamation</h3>
                        <p><strong>Référence:</strong> <span id="claim-ref"><?php echo htmlspecialchars($claimRef ?? ''); ?></span></p>
                        <p><strong>Type:</strong> <span id="claim-type"><?php echo htmlspecialchars($claimType ?? ''); ?></span></p>
                        <p><strong>Date de soumission:</strong> <span id="claim-date"><?php echo htmlspecialchars($claimDate ?? ''); ?></span></p>
                        <p><strong>Description:</strong></p>
                        <div class="claim-description" id="claim-description">
                            <?php echo htmlspecialchars($claimDescription ?? 'Aucune description disponible.'); ?>
                        </div>
                        <p><strong>Pièces jointes:</strong></p>
                        <div class="attachments">
                            <?php if (!empty($piecesJointes)): ?>
                                <?php foreach ($piecesJointes as $attachment): ?>
                                    <div class="attachment">
                                        <i class="fas fa-image"></i>
                                        <span><?php echo htmlspecialchars($attachment); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Aucune pièce jointe.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de traitement de la réclamation -->
                <div class="claim-response">
                    <h3>Traitement de la réclamation</h3>
                    <div class="template-selector">
                        <label for="response-template">Utiliser un modèle de réponse:</label>
                        <select id="response-template" class="form-control">
                            <option value="">-- Sélectionnez un modèle --</option>
                            <option value="fuite_externe">Fuite externe - Intervention requise</option>
                            <option value="facture_erreur">Facture - Erreur confirmée</option>
                            <option value="facture_correcte">Facture - Montant correct</option>
                        </select>
                    </div>

                    <form id="response-form">
                        <div class="form-group">
                            <label for="response-text">Réponse:</label>
                            <textarea id="response-text" rows="10" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Statut:</label>
                            <div>
                                <label class="radio-inline">
                                    <input type="radio" name="claim-status" value="processing" checked> En traitement
                                </label>
                                <label class="radio-inline" style="margin-left: 15px;">
                                    <input type="radio" name="claim-status" value="resolved"> Résolue
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


</div>
</div>

    
    <script src="js/claims-frs.js"></script>
</body>
</html>
