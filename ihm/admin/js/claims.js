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
