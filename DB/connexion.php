<?php

class Database {
    // Suppression du type nullable pour compatibilité avec les versions antérieures de PHP
    private static $instance = null;
    private $pdo; 
    
    // Rendre le constructeur privé pour utiliser le pattern Singleton
    private function __construct() {
        $host = '127.0.0.1';
        $dbName = 'electro';
        $user = 'root';
        $password = '';
        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
        
        try {
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
    
    // Méthode statique pour obtenir l'instance unique de la connexion
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    // Retourne l'objet PDO
    public function getConnection(): PDO {
        return $this->pdo;
    }
}