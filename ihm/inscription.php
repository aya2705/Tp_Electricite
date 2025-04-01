<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion des Factures d'Électricité</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Gestion des Factures d'Électricité</h1>
        </div>
        <?php
        // Mapping des erreurs spécifiques à l'inscription
        $errorMapping = [
            'emptyFields' => "Tous les champs doivent être renseignés.",
            'clientNotFoundOrLinked' => "Client introuvable ou déjà lié.",
            'emailExists' => "Cette adresse email est déjà utilisée.",
            'registrationFailed' => "Erreur lors de l'inscription, réessayez plus tard.",
        ];
        if (isset($_GET['error']) && array_key_exists($_GET['error'], $errorMapping)) {
            echo '<div class="error-message">' . $errorMapping[$_GET['error']] . '</div>';
        }
        ?>
        <div class="login-form">
            <h2>Créez votre compte</h2>
            <form method="POST" action="../traitement/userService.php?action=register">
                <div class="form-group">
                    <label for="client_id">Numéro de client</label>
                    <input type="text" id="client_id" name="client_id" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                </div>
            </form>
            <div class="register-option">
                <p>Vous avez déjà un compte ? <a href="/ihm/connexion.php">Connectez-vous</a></p>
            </div>
        </div>
    </div>
</body>

</html>