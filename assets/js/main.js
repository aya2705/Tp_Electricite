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

// Ouvrir un modal photo
function openPhotoModal(imageSrc) {
    const modal = document.getElementById("photo-modal");
    if (modal) {
        const modalImage = document.getElementById("modal-image");
        if (modalImage) {
            modalImage.src = imageSrc;
        }
        modal.style.display = 'block';
    }
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