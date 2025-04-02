document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du graphique
    initComparisonChart();
    
    // Gestion des modals
    setupModalEvents();
    
    // Gestion des formulaires de modification
    setupForms();
});

// Initialisation du graphique de comparaison client/agent
function initComparisonChart() {
    const ctx = document.getElementById('comparison-chart');
    if (!ctx) return;
    
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    
    // Données de consommation (client vs agent)
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Ma saisie',
                    data: [290, 300, 310, 305, 315, 325, 340, 335, 330, 315, 325, null],
                    backgroundColor: 'rgba(52, 152, 219, 0.5)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Relevé agent',
                    data: [295, 305, 315, 300, 310, 330, 345, 330, 325, 320, 330, null],
                    backgroundColor: 'rgba(46, 204, 113, 0.5)',
                    borderColor: 'rgba(46, 204, 113, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'kWh'
                    }
                }
            }
        }
    });
}

// Configuration des événements modaux
function setupModalEvents() {
    // Modifier l'email
    const editEmailBtn = document.getElementById('edit-email');
    if (editEmailBtn) {
        editEmailBtn.addEventListener('click', function() {
            openModal('email-modal');
        });
    }
    
    // Modifier le téléphone
    const editPhoneBtn = document.getElementById('edit-phone');
    if (editPhoneBtn) {
        editPhoneBtn.addEventListener('click', function() {
            openModal('phone-modal');
        });
    }
    
    // Changer le mot de passe
    const changePasswordBtn = document.getElementById('change-password-btn');
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function() {
            openModal('password-modal');
        });
    }
}

// Configuration des formulaires
function setupForms() {
    // Formulaire d'email
    const emailForm = document.getElementById('email-form');
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const newEmail = document.getElementById('new-email').value;
            if (!newEmail) return;
            
            showAlert('Votre email a été mis à jour', 'success');
            document.querySelector('.info-value').innerText = newEmail + ' ';
            document.querySelector('.info-value').appendChild(document.getElementById('edit-email'));
            closeModal('email-modal');
            emailForm.reset();
        });
    }
    
    // Formulaire de téléphone
    const phoneForm = document.getElementById('phone-form');
    if (phoneForm) {
        phoneForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const newPhone = document.getElementById('new-phone').value;
            if (!newPhone) return;
            
            showAlert('Votre numéro de téléphone a été mis à jour', 'success');
            document.querySelectorAll('.info-value')[1].innerText = newPhone + ' ';
            document.querySelectorAll('.info-value')[1].appendChild(document.getElementById('edit-phone'));
            closeModal('phone-modal');
            phoneForm.reset();
        });
    }
    
    // Formulaire de mot de passe
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-new-password').value;
            
            if (newPassword !== confirmPassword) {
                showAlert('Les mots de passe ne correspondent pas', 'danger');
                return;
            }
            
            showAlert('Votre mot de passe a été modifié avec succès', 'success');
            closeModal('password-modal');
            passwordForm.reset();
        });
    }
}
