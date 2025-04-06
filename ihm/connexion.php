<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion des Factures d'Électricité</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <!-- <link rel="stylesheet" href="../assets/css/main.css"> -->
    <style>

.login-container {
    max-width: 500px;
    margin: 80px auto;
    background-color: white;
    border-radius: 8px;
    box-shadow: var(--shadow);
    padding: 30px;
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h1 {
    color: var(--primary-color);
    margin-bottom: 20px;
    font-size: 24px;
}

.switch-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
}

.switch-container span {
    margin: 0 10px;
    color: #95a5a6;
    font-weight: 500;
}

.switch-container span.active {
    color: var(--secondary-color);
    font-weight: bold;
}

/* Switch toggle */
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: var(--secondary-color);
}

input:focus + .slider {
    box-shadow: 0 0 1px var(--secondary-color);
}

input:checked + .slider:before {
    transform: translateX(30px);
}

.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}

/* Form styles */
.login-form, .register-form {
    padding: 20px 0;
}

.login-form h2, .register-form h2 {
    font-size: 22px;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}

.forgot-password {
    text-decoration: none;
    color: var(--secondary-color);
    font-size: 14px;
}

.forgot-password:hover {
    text-decoration: underline;
}

.register-option {
    margin-top: 30px;
    text-align: center;
    border-top: 1px solid var(--border-color);
    padding-top: 20px;
}

.register-option p {
    color: #7f8c8d;
}

.register-option a {
    color: var(--secondary-color);
    text-decoration: none;
    font-weight: bold;
}

.register-option a:hover {
    text-decoration: underline;
}


    </style>
   
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Gestion des Factures d'Électricité</h1>
        </div>
        <?php
        // Ajout du mapping pour les codes d'erreur
        $errorMapping = [
            'invalidCredentials' => "Email ou mot de passe incorrect.",
            'emptyFields' => "Tous les champs doivent être renseignés.",
            // ...autres codes d'erreur...
        ];
        if (isset($_GET['error']) && array_key_exists($_GET['error'], $errorMapping)) {
            echo '<div class="error-message">' . $errorMapping[$_GET['error']] . '</div>';
        }

        // Ajout du mapping pour les messages de succès
        $successMapping = [
            'registered' => "Inscription réussie ! Veuillez vous connecter.",
            // ...autres codes de succès...
        ];
        if (isset($_GET['success']) && array_key_exists($_GET['success'], $successMapping)) {
            echo '<div class="success-message">' . $successMapping[$_GET['success']] . '</div>';
        }
        ?>
        <div class="login-form">
            <h2>Connexion</h2>
            <form method="POST" action="../traitement/userService.php?action=authenticate">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </div>
            </form>
            <div class="register-option">
                <p>Pas de compte ? <a href="/ihm/inscription.php">Inscrivez-vous</a></p>
            </div>
        </div>
    </div>
    <script src="../assets/js/login.js"></script>
</body>

</html>