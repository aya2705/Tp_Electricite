<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Gestion des Factures</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .profile-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-right: 20px;
        }
        
        .profile-details h2 {
            margin: 0 0 5px 0;
            color: var(--primary-color);
        }
        
        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .info-label {
            width: 30%;
            font-weight: bold;
            color: var(--dark-color);
        }
        
        .info-value {
            width: 70%;
        }
        
        .edit-btn {
            color: var(--secondary-color);
            cursor: pointer;
            margin-left: 10px;
        }
        
        .chart-container {
            height: 400px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Espace Client</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.html">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="consumption.html">
                    <i class="fas fa-bolt"></i> Saisie Consommation
                </a>
                <a href="claims.html">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="profile.html" class="active">
                    <i class="fas fa-user"></i> Mon Profil
                </a>
                <a href="../index.html" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <h1>Mon Profil</h1>
            
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-details">
                        <h2>Jean Dupont</h2>
                        <p>Client depuis Avril 2020</p>
                        <p>Numéro de client: #CL-2020-1245</p>
                    </div>
                </div>
                
                <div class="profile-info">
                    <div class="info-row">
                        <div class="info-label">Adresse</div>
                        <div class="info-value">15 Avenue de la République, 75011 Paris</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value">jean.dupont@example.com <i class="fas fa-pencil-alt edit-btn" id="edit-email"></i></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">+33 6 12 34 56 78 <i class="fas fa-pencil-alt edit-btn" id="edit-phone"></i></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Type de compteur</div>
                        <div class="info-value">Standard (Électromécanique)</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Numéro de compteurs</div>
                        <div class="info-value">E-56789-2020</div>
                        <div class="info-value">E-63790-2020</div>
                    </div>
                    
                </div>

            </div>
            
        </div>
    </div>
    
    <!-- Modal pour modifier l'email -->
    <div id="email-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Modifier votre email</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="email-form">
                    <div class="form-group">
                        <label for="new-email">Nouvel email</label>
                        <input type="email" id="new-email" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirmez votre mot de passe</label>
                        <input type="password" id="confirm-password" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal pour modifier le téléphone -->
    <div id="phone-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Modifier votre téléphone</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="phone-form">
                    <div class="form-group">
                        <label for="new-phone">Nouveau numéro</label>
                        <input type="tel" id="new-phone" required pattern="[0-9\+\s]{10,15}">
                        <small>Format: +33 6 12 34 56 78</small>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal pour changer le mot de passe -->
    <div id="password-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Changer de mot de passe</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="password-form">
                    <div class="form-group">
                        <label for="current-password">Mot de passe actuel</label>
                        <input type="password" id="current-password" required>
                    </div>
                    <div class="form-group">
                        <label for="new-password">Nouveau mot de passe</label>
                        <input type="password" id="new-password" required minlength="8">
                        <small>8 caractères minimum, incluant majuscules, minuscules et chiffres</small>
                    </div>
                    <div class="form-group">
                        <label for="confirm-new-password">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="confirm-new-password" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/profile.js"></script>
</body>
</html>
