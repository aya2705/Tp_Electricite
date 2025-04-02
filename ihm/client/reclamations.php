<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réclamations - Gestion des Factures</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .claim-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .claim-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .claim-icon i {
            font-size: 20px;
            color: var(--secondary-color);
        }
        
        .claim-content {
            flex-grow: 1;
        }
        
        .claim-meta {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #6c757d;
            margin-top: 8px;
        }
        
        .claim-status {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 15px;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-resolved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .claim-type {
            display: inline-block;
            background-color: #e9ecef;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .attachment-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .attachment-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .attachment-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .attachment-item .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255,255,255,0.7);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div la="sidebar-header">
                <h2>Espace Client</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.html">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="consumption.html">
                    <i class="fas fa-bolt"></i> Saisie Consommation
                </a>
                <a href="claims.html" class="active">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="profile.html">
                    <i class="fas fa-user"></i> Mon Profil
                </a>
                <a href="../index.html" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <h1>Mes Réclamations</h1>
            
            <div class="card">
                <div class="card-header">
                    <h2>Réclamations en cours</h2>
                </div>
                
                <div class="claims-list">
                    <div class="claim-item">
                        <div class="claim-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="claim-content">
                            <h3>Fuite d'électricité externe</h3>
                            <p>J'ai constaté une fuite d'électricité à l'extérieur du compteur qui pourrait affecter mes relevés.</p>
                            <div class="claim-meta">
                                <div>
                                    <span class="claim-type">Fuite externe</span>
                                    <span>Référence: #REF-2023-45</span>
                                </div>
                                <span>Soumise le 12/11/2023</span>
                            </div>
                        </div>
                        <span class="claim-status status-pending">En traitement</span>
                    </div>
                    
                    <div class="claim-item">
                        <div class="claim-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="claim-content">
                            <h3>Contestation de facture</h3>
                            <p>Je conteste le montant de ma facture d'octobre qui me semble anormalement élevé.</p>
                            <div class="claim-meta">
                                <div>
                                    <span class="claim-type">Facture</span>
                                    <span>Référence: #REF-2023-42</span>
                                </div>
                                <span>Soumise le 05/11/2023</span>
                            </div>
                        </div>
                        <span class="claim-status status-resolved">Résolue</span>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Nouvelle Réclamation</h2>
                </div>
                
                <form id="new-claim-form">
                    <div class="form-group">
                        <label for="claim-type">Type de réclamation</label>
                        <select id="claim-type" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="fuite_externe">Fuite externe</option>
                            <option value="fuite_interne">Fuite interne</option>
                            <option value="facture">Facture</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="other-type-group" style="display: none;">
                        <label for="other-type">Précisez le type</label>
                        <input type="text" id="other-type">
                    </div>
                    
                    <div class="form-group">
                        <label for="claim-description">Description détaillée</label>
                        <textarea id="claim-description" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="claim-attachments">Pièces jointes (photos, documents...)</label>
                        <input type="file" id="claim-attachments" multiple accept="image/*,.pdf">
                        <div class="attachment-preview" id="attachment-preview"></div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Soumettre la réclamation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    <script src="js/claims.js"></script>
</body>
</html>
