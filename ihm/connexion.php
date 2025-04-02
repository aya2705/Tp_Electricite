<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion des Factures d'Électricité</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/login.css">
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
            'emptyFields'        => "Tous les champs doivent être renseignés.",
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
            <form id="login-form" method="POST" action="traitement/authenticationService.php">
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
</body>
</html>
