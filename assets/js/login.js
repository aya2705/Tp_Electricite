/* filepath: /Users/youns/Desktop/php/TP2/assets/js/login.js */
document.addEventListener('DOMContentLoaded', function() {
    // Toggle entre client et fournisseur
    const userTypeToggle = document.getElementById('user-type-toggle');
    const switchLabels = document.querySelectorAll('.switch-container span');
    
    userTypeToggle.addEventListener('change', function() {
        // Inverser la classe active entre les deux labels
        switchLabels.forEach(label => {
            label.classList.toggle('active');
        });
    });
    
    // Toggle entre formulaires de connexion et d'inscription
    const registerLink = document.getElementById('register-link');
    const backToLoginBtn = document.getElementById('back-to-login');
    const loginForm = document.querySelector('.login-form');
    const registerForm = document.querySelector('.register-form');
    
    registerLink.addEventListener('click', function(e) {
        e.preventDefault();
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
    });
    
    backToLoginBtn.addEventListener('click', function() {
        registerForm.style.display = 'none';
        loginForm.style.display = 'block';
    });
    
    // Soumission du formulaire de connexion
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const isAdmin = userTypeToggle.checked;
        
        // Simulation de la connexion
        console.log(`Tentative de connexion: ${email}, Type: ${isAdmin ? 'Fournisseur' : 'Client'}`);
        
        // Redirection basée sur le type d'utilisateur (client/fournisseur)
        if (isAdmin) {
            window.location.href = 'admin/dashboard.html';
        } else {
            window.location.href = 'client/dashboard.html';
        }
    });
    
    // Soumission du formulaire d'inscription
    document.getElementById('register-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('reg-name').value;
        const email = document.getElementById('reg-email').value;
        const address = document.getElementById('reg-address').value;
        const password = document.getElementById('reg-password').value;
        const confirmPassword = document.getElementById('reg-confirm').value;
        
        // Vérification que les mots de passe correspondent
        if (password !== confirmPassword) {
            alert('Les mots de passe ne correspondent pas.');
            return;
        }
        
        // Simulation de l'inscription
        console.log(`Inscription: ${name}, ${email}, ${address}`);
        
        // Afficher un message de succès et revenir au formulaire de connexion
        alert('Inscription réussie! Vous pouvez maintenant vous connecter.');
        registerForm.style.display = 'none';
        loginForm.style.display = 'block';
    });
});