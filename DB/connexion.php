<?php
// DB/connexion.php

/**
 * Établit une connexion PDO à la base de données
 * @return PDO
 * @throws PDOException Si la connexion échoue
 */
function creerConnexion() {
    // Configuration de la base de données
    $config = [
        'host'     => 'localhost',
        'dbname'   => 'gestion_factures',
        'username' => 'root',      // À remplacer par vos identifiants
        'password' => '',          // À remplacer par votre mot de passe
        'options'  => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    ];

    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8";
        return new PDO($dsn, $config['username'], $config['password'], $config['options']);
    } catch (PDOException $e) {
        // Journalisation de l'erreur (à adapter selon votre environnement)
        error_log("Erreur de connexion DB: " . $e->getMessage());
        
        // Message générique pour l'utilisateur
        die("Impossible de se connecter à la base de données. Veuillez réessayer plus tard.");
    }
}

// Constantes pour un accès global (optionnel)
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_factures');
define('DB_USER', 'root');
define('DB_PASS', '');