document.addEventListener('DOMContentLoaded', function() {
     // Éléments principaux
     const processButtons = document.querySelectorAll('.action-process');
     const resolveButtons = document.querySelectorAll('.action-resolve');
     const searchInput = document.getElementById('search-claim');
     const responseForm = document.getElementById('response-form');
     
     // Traitement des réclamations
     if (processButtons) {
         processButtons.forEach(btn => {
             btn.addEventListener('click', function() {
                 const claimId = this.getAttribute('data-id');
                 document.getElementById('claim-ref').textContent = `#${claimId}`;
                 openModal('process-claim-modal');
             });
         });
     }
     
     // Résolution directe
     if (resolveButtons) {
         resolveButtons.forEach(btn => {
             btn.addEventListener('click', function() {
                 const claimId = this.getAttribute('data-id');
                 showAlert(`Réclamation #${claimId} résolue`, 'success');
             });
         });
     }
     
     // Modèles de réponse simplifiés
     const responseTemplate = document.getElementById('response-template');
     if (responseTemplate) {
         responseTemplate.addEventListener('change', function() {
             const responseText = document.getElementById('response-text');
             if (!responseText) return;
             
             if (this.value) {
                 responseText.value = `Cher client,\n\nNous avons traité votre réclamation.\n\nCordialement,\nLe service client`;
             }
         });
     }
     
     // Formulaire de réponse
     if (responseForm) {
         responseForm.addEventListener('submit', function(e) {
             e.preventDefault();
             showAlert('Réponse envoyée au client', 'success');
             closeModal('process-claim-modal');
             responseForm.reset();
         });
     }
     
     // Recherche simplifiée
     if (searchInput) {
         searchInput.addEventListener('input', function() {
             const query = this.value.toLowerCase();
             document.querySelectorAll('.claim-item').forEach(item => {
                 if (item.textContent.toLowerCase().includes(query)) {
                     item.style.display = '';
                 } else {
                     item.style.display = 'none';
                 }
             });
         });
     }
 });
 
 
 // ??
 // Ouvrir un modal
 function openModal(modalId) {
     const modal = document.getElementById(modalId);
     if (modal) {
         modal.style.display = 'block';
     }
 }
 
 // Fermer un modal
 function closeModal(modalId) {
     const modal = document.getElementById(modalId);
     if (modal) {
         modal.style.display = 'none';
     }
 }
 
 // Afficher une alerte
 function showAlert(message, type = 'info') {
     // Créer l'élément alerte
     const alertDiv = document.createElement('div');
     alertDiv.className = `alert alert-${type}`;
     alertDiv.textContent = message;
     
     // Trouver l'élément où afficher l'alerte
     const mainContent = document.querySelector('.main-content');
     if (mainContent) {
         mainContent.insertBefore(alertDiv, mainContent.firstChild);
         
         // Supprimer l'alerte après quelques secondes
         setTimeout(() => {
             alertDiv.remove();
         }, 5000);
     }
 }
 
 // Fonction pour formater un prix
 function formatPrice(price) {
     return new Intl.NumberFormat('fr-FR', { 
         style: 'currency', 
         currency: 'XOF'
     }).format(price);
 }
 
 // Configuration des modals
 document.addEventListener('DOMContentLoaded', function() {
     // Fermeture des modals avec le bouton X
     document.querySelectorAll('.modal .close').forEach(closeBtn => {
         closeBtn.addEventListener('click', function() {
             const modal = this.closest('.modal');
             modal.style.display = 'none';
         });
     });
     
     // Fermeture des modals en cliquant à l'extérieur
     window.addEventListener('click', function(e) {
         document.querySelectorAll('.modal').forEach(modal => {
             if (e.target === modal) {
                 modal.style.display = 'none';
             }
         });
     });
 });


 // Fonction pour remplir les informations dans le modal
 function fillClaimDetails(claimData) {
    document.getElementById('client-name').textContent = claimData.clientName;
    document.getElementById('client-id').textContent = claimData.clientId;
    document.getElementById('client-address').textContent = claimData.clientAddress;
    document.getElementById('client-email').textContent = claimData.clientEmail;
    document.getElementById('client-phone').textContent = claimData.clientPhone;
    document.getElementById('claim-ref').textContent = claimData.claimRef;
    document.getElementById('claim-type').textContent = claimData.claimType;
    document.getElementById('claim-date').textContent = claimData.claimDate;
    document.getElementById('claim-description').textContent = claimData.claimDescription;

    // Afficher les pièces jointes
    let attachmentsContainer = document.querySelector('.attachments');
    attachmentsContainer.innerHTML = ''; // Reset current attachments
    claimData.attachments.forEach(function (attachment) {
        let attachmentElement = document.createElement('div');
        attachmentElement.classList.add('attachment');
        attachmentElement.innerHTML = `<i class="fas fa-image"></i><span>${attachment}</span>`;
        attachmentsContainer.appendChild(attachmentElement);
    });
}

// Exemple d'appel AJAX pour récupérer les données de la réclamation
function loadClaimDetails(claimId) {
    fetch(`/getClaimDetails.php?id=${claimId}`)
        .then(response => response.json())
        .then(data => {
            fillClaimDetails(data);
        })
        .catch(error => console.error('Erreur:', error));
}

// Exemple d'appel pour charger les données de la réclamation avec un ID spécifique
loadClaimDetails(123); // Remplace 123 par l'ID réel de la réclamation



