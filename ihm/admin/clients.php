<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fournisseur') {
    header("Location: ../connexion.php");
    exit;
}

require_once '../../DB/ClientDAO.php';
require_once '../../DB/compteurDAO.php';

$clientDAO = new ClientDAO();
$compteurDAO = new CompteurDAO();
$clients = $clientDAO->getAllClients() ?? [];

$messages = [
    'clientAdded' => 'Le client a été ajouté avec succès.',
    'clientEdited' => 'Le client a été modifié avec succès.',
    'clientDeleted' => 'Le client a été supprimé avec succès.',
    'emptyFields' => 'Veuillez remplir tous les champs.',
    'clientFailed' => 'Erreur lors de l’ajout du client.',
    'editFailed' => 'Erreur lors de la modification du client.',
    'deleteFailed' => 'Erreur lors de la suppression du client.',
    'missingId' => 'ID du client manquant.'
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Clients - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ...existing styles généraux... */
        .filters {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .search-container {
            flex-grow: 1;
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }

        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .client-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .client-table th,
        .client-table td {
            padding: 12px 15px;
            text-align: left;
        }

        .client-table th {
            background-color: #f8f9fa;
            color: var(--primary-color);
            font-weight: bold;
        }

        .client-table tr:not(:last-child) {
            border-bottom: 1px solid var(--border-color);
        }

        .client-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .client-actions a {
            cursor: pointer;
            color: var(--secondary-color);
            padding: 5px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .client-actions a:hover {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
        }

        .client-actions .delete {
            color: var(--danger-color);
        }

        .client-actions .delete:hover {
            background-color: rgba(231, 76, 60, 0.1);
            color: darkred;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header .close {
            font-size: 28px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Espace Fournisseur</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="clients.php" class="active">
                    <i class="fas fa-users"></i> Gestion des clients
                </a>
                <a href="claims.php">
                    <i class="fas fa-exclamation-circle"></i> Réclamations
                </a>
                <a href="consumption.php">
                    <i class="fas fa-bolt"></i> Gestion des saisies
                </a>
                <a href="settings.php">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Gestion des Clients</h1>

            <!-- Affichage des messages mappés -->
            <?php if (isset($_GET['success'])):
                $message = $messages[$_GET['success']] ?? htmlspecialchars($_GET['success']);
                ?>
                <div class="alert alert-success">
                    <?= $message ?>
                </div>
            <?php elseif (isset($_GET['error'])):
                $message = $messages[$_GET['error']] ?? htmlspecialchars($_GET['error']);
                ?>
                <div class="alert alert-danger">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="filters">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher un client..." id="search-client">
                </div>
                <button class="btn btn-primary" data-modal="addClientModal">Ajouter un Client</button>
            </div>

            <table class="client-table">
                <thead>
                    <tr>
                        <th>ID Client</th>
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Compteurs</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client):
                        $compteurs = $compteurDAO->getCompteursByClientId($client->getClientId());
                        $numeroSeries = array_map(function ($compteur) {
                            return htmlspecialchars($compteur->getNumeroSerie());
                        }, $compteurs);
                        $compteurDisplay = !empty($numeroSeries) ? implode(', ', $numeroSeries) : 'Aucun';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($client->getClientId()) ?></td>
                            <td><?= htmlspecialchars($client->getFullName()) ?></td>
                            <td><?= htmlspecialchars($client->getAddress()) ?></td>
                            <td><?= htmlspecialchars($client->getPhone()) ?></td>
                            <td><?= $compteurDisplay ?></td>
                            <td class="client-actions">
                                <a class="btn edit open-edit-modal"
                                    data-client-id="<?= htmlspecialchars($client->getClientId()) ?>"
                                    data-full-name="<?= htmlspecialchars($client->getFullName()) ?>"
                                    data-phone="<?= htmlspecialchars($client->getPhone()) ?>"
                                    data-address="<?= htmlspecialchars($client->getAddress()) ?>"
                                    data-compteurs="<?= implode(', ', $numeroSeries) ?>"> <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn delete"
                                    href="../../traitement/ClientService.php?action=delete&client_id=<?= htmlspecialchars($client->getClientId()) ?>"
                                    onclick="return confirm('Confirmez-vous la suppression de ce client ?');"> <i
                                        class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal pour ajouter un client -->
    <div id="addClientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ajouter un nouveau client</h2>
                <span class="close">&times;</span>
            </div>
            <form method="POST" action="../../traitement/ClientService.php?action=add">
                <div class="form-group">
                    <label for="add_full_name">Nom complet</label>
                    <input type="text" id="add_full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="add_phone">Téléphone</label>
                    <input type="tel" id="add_phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="add_address">Adresse</label>
                    <input type="text" id="add_address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="add_compteurs">Numéros de compteurs (séparés par des virgules)</label>
                    <input type="text" id="add_compteurs" name="compteurs" placeholder="Ex: C000000, C111111">
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- Nouveau modal pour modifier un client -->
    <div id="editClientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Modifier le Client</h2>
                <span class="close">&times;</span>
            </div>
            <form method="POST" action="../../traitement/ClientService.php?action=edit">
                <input type="hidden" id="edit_client_id" name="client_id">
                <div class="form-group">
                    <label for="edit_full_name">Nom complet</label>
                    <input type="text" id="edit_full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_phone">Téléphone</label>
                    <input type="tel" id="edit_phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="edit_address">Adresse</label>
                    <input type="text" id="edit_address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="edit_compteurs">Numéros de compteurs (séparés par des virgules)</label>
                    <input type="text" id="edit_compteurs" name="compteurs" placeholder="Ex: C000000, C111111">
                </div>
                <button type="submit" class="btn btn-primary">Modifier</button>
            </form>
        </div>
    </div>

    <script>
        // Ouvrir le modal d'ajout
        document.querySelectorAll('[data-modal="addClientModal"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('addClientModal').style.display = 'block';
            });
        });
        // Ouvrir le modal de modification et pré-remplir les champs
        document.querySelectorAll('.open-edit-modal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('editClientModal').style.display = 'block';
                document.getElementById('edit_client_id').value = this.dataset.clientId;
                document.getElementById('edit_full_name').value = this.dataset.fullName;
                document.getElementById('edit_phone').value = this.dataset.phone;
                document.getElementById('edit_address').value = this.dataset.address;
                document.getElementById('edit_compteurs').value = this.dataset.compteurs;
            });
        });
        // Fermer les modals
        document.querySelectorAll('.modal .close').forEach(function (span) {
            span.addEventListener('click', function () {
                this.closest('.modal').style.display = 'none';
            });
        });
        // Fermer le modal en cliquant à l'extérieur
        window.addEventListener('click', function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });
    </script>
</body>

</html>