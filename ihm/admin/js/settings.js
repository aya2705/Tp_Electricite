document.addEventListener('DOMContentLoaded', function() {
    // Formulaires principaux
    const forms = document.querySelectorAll('.settings-form');
    forms.forEach(form => {
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                showAlert('Paramètres enregistrés', 'success');
            });
        }
    });
    
    // Réinitialisation des paramètres
    const resetBtn = document.querySelector('.btn-secondary');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            showAlert('Paramètres réinitialisés', 'success');
        });
    }
    
    // Gestion simplifiée des modèles
    const addTemplateBtn = document.getElementById('add-template-btn');
    if (addTemplateBtn) {
        addTemplateBtn.addEventListener('click', function() {
            openModal('template-modal');
        });
    }
    
    const templateForm = document.getElementById('template-form');
    if (templateForm) {
        templateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showAlert('Modèle enregistré', 'success');
            closeModal('template-modal');
        });
    }
    
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            openModal('template-modal');
        });
    });
    
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            showAlert('Modèle supprimé', 'success');
        });
    });
    
    // Changement de mot de passe simplifié
    const changePasswordBtn = document.getElementById('change-password-btn');
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function() {
            openModal('password-modal');
        });
    }
    
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showAlert('Mot de passe modifié', 'success');
            closeModal('password-modal');
            passwordForm.reset();
        });
    }
});
