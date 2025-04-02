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
                             <!-- <button class="action-resolve" data-id="REF-<?php echo $reclamation['reclamation_id']; ?>" title="Marquer comme résolu">
                                 <i class="fas fa-check"></i>
                             </button> -->
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
     <div id="process-claim-modal" class="modal fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
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
                        <div class="claim-description p-2 bg-white rounded border border-gray-300">{{ claimDescription }}</div>
                        <p class="mt-3"><strong>Pièces jointes:</strong></p>
                        <div class="attachments grid grid-cols-2 gap-2">
                            {{#each piecesJointes}}
                            <div class="attachment flex items-center space-x-2 bg-white p-2 rounded border border-gray-300">
                                <i class="fas fa-paperclip text-gray-500"></i>
                                <a href="/Reclamation/uploads/{{ this }}" target="_blank" class="text-blue-500 hover:underline">{{ this }}</a>
                            </div>
                            {{/each}}
                        </div>
                    </div>
                </div>
                <div class="claim-response bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-700">Traitement de la réclamation</h3>
                    <div class="template-selector mt-3">
                        <label for="response-template" class="block text-sm font-medium text-gray-600">Utiliser un modèle de réponse:</label>
                        <select id="response-template" class="form-control w-full p-2 mt-1 border border-gray-300 rounded">
                            <option value="">-- Sélectionnez un modèle --</option>
                            <option value="fuite_externe">Fuite externe - Intervention requise</option>
                            <option value="facture_erreur">Facture - Erreur confirmée</option>
                            <option value="facture_correcte">Facture - Montant correct</option>
                        </select>
                    </div>
                    <form id="response-form" method="POST" action="../../traitement/ReclamationService.php">
                        <input type="hidden" name="action" value="respond">
                        <input type="hidden" name="reclamation_id" value="<?= $reclamation_id ?? ''; ?>">

                        <div class="form-group">
                            <label for="response-text">Réponse:</label>
                            <textarea id="response-text" rows="10" name="response_text" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Statut:</label>
                            <div>
                                <label class="radio-inline">
                                    <input type="radio" name="claim_status" value="processing" checked> En traitement
                                </label>
                                <label class="radio-inline" style="margin-left: 15px;">
                                    <input type="radio" name="claim_status" value="resolved"> Résolue
                                </label>
                            </div>
                        </div>
    
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
                        </div>
                    </form>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Réponse envoyée et statut mis à jour !</div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

     

 
 </div>
 </div>

 <script>
 document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (event) {
        if (event.target.closest(".action-process")) { // Vérifie si un bouton est cliqué
            let button = event.target.closest(".action-process");
            let reclamationId = button.getAttribute("data-id").replace("REF-", ""); 
            let detailsContainer = document.querySelector(".claim-detail-info");

            if (!detailsContainer) {
                console.error("Erreur : le conteneur des détails de réclamation est introuvable.");
                return;
            }

            fetch("../../traitement/ReclamationService.php?reclamationId=" + reclamationId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur réseau");
                    }
                    return response.text();
                })
                .then(data => {
                    detailsContainer.innerHTML = data; // Injecte les détails dans la div
                })
                .catch(error => console.error("Erreur :", error));
        }
    });
});


   


 </script>
 
     
     <script src="js/claims-frs.js"></script>
 </body>
 </html>