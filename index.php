<?php
session_start();

/* // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord approprié
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'fournisseur') {
        header('Location: ihm/admin/dashboard.php');
        exit;
    } else {
        header('Location: ihm/client/dashboard.php');
        exit;
    }
} */

// Sinon, afficher la page de connexion
include_once 'ihm/connexion.php';