/* filepath: /Users/youns/Desktop/php/TP2/assets/js/main.js */
// Fonctions communes essentielles

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