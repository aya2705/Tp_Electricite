<?php
require_once 'connexion.php';
require_once __DIR__ . '/../models/compteur.php';

class CompteurDAO {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getCompteursByClientId($client_id) {
        $stmt = $this->db->prepare("SELECT * FROM compteurs WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $client_id]);
        $rows = $stmt->fetchAll();
        $compteurs = [];
        foreach ($rows as $row) {
            $compteurs[] = new Compteur($row['compteur_id'], $row['client_id'], $row['numero_serie']);
        }
        return $compteurs;
    }

    public function createCompteur($client_id, $numero_serie) {
        $stmt = $this->db->prepare("INSERT INTO compteurs (client_id, numero_serie) VALUES (:client_id, :numero_serie)");
        $stmt->execute(['client_id' => $client_id, 'numero_serie' => $numero_serie]);
        return $this->db->lastInsertId();
    }

    public function deleteCompteursByClientId($client_id) {
        $stmt = $this->db->prepare("DELETE FROM compteurs WHERE client_id = :client_id");
        return $stmt->execute(['client_id' => $client_id]);
    }
}
