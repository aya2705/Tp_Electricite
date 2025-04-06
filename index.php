<?php
session_start();

// Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord approprié
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'fournisseur':
            header('Location: ihm/admin/dashboard.php');
            exit;
        case 'client':
            header('Location: ihm/client/dashboard.php');
            exit;
        case 'agent':
            header('Location: ihm/agent/dashboard.php');
            exit;
    }
}

// Sinon, afficher la page de connexion
include_once 'ihm/connexion.php';