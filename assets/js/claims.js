document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const claimTypeSelect = document.getElementById('claim-type');
    const otherTypeGroup = document.getElementById('other-type-group');
    const attachmentsInput = document.getElementById('claim-attachments');
    const attachmentPreview = document.getElementById('attachment-preview');
    const claimForm = document.getElementById('new-claim-form');
    
    // Afficher le champ "Autre" si nécessaire
    if (claimTypeSelect) {
        claimTypeSelect.addEventListener('change', function() {
            otherTypeGroup.style.display = this.value === 'autre' ? 'block' : 'none';
        });
    }
    
    // Gestion des pièces jointes
    if (attachmentsInput) {
        attachmentsInput.addEventListener('change', function(e) {
            // Vider l'aperçu
            attachmentPreview.innerHTML = '';
            
            // Afficher chaque fichier sélectionné
            Array.from(e.target.files).forEach(function(file, index) {
                const attachmentItem = document.createElement('div');
                attachmentItem.className = 'attachment-item';
                
                if (file.type.startsWith('image/')) {
                    // Pour les images
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        attachmentItem.innerHTML = `
                            <img src="${e.target.result}" alt="Aperçu">
                            <div class="remove-btn" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Pour les autres fichiers
                    attachmentItem.innerHTML = `
                        <div class="file-icon">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                        <div class="remove-btn" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="file-name">${file.name}</div>
                    `;
                }
                
                attachmentPreview.appendChild(attachmentItem);
            });
            
            // Ajouter les événements de suppression
            attachmentPreview.querySelectorAll('.remove-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    this.closest('.attachment-item').remove();
                });
            });
        });
    }
    
    // Soumission du formulaire
    if (claimForm) {
        claimForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const claimType = claimTypeSelect.value;
            const description = document.getElementById('claim-description').value;
            
            if (!claimType) {
                showAlert('Veuillez sélectionner un type de réclamation', 'warning');
                return;
            }
            
            if (!description) {
                showAlert('Veuillez fournir une description de votre réclamation', 'warning');
                return;
            }
            
            if (claimType === 'autre' && !document.getElementById('other-type').value) {
                showAlert('Veuillez préciser le type de votre réclamation', 'warning');
                return;
            }
            
            // Simulation d'envoi
            showAlert('Votre réclamation a été soumise avec succès! Référence: #REF-2023-46', 'success');
            
            // Réinitialiser le formulaire
            claimForm.reset();
            attachmentPreview.innerHTML = '';
            otherTypeGroup.style.display = 'none';
            
            // Redirection différée
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 2000);
        });
    }
});




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