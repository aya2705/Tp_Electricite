<?php
require_once 'connexion.php';

class TarificationDAO {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getTarification() {
        $stmt = $this->db->prepare("SELECT * FROM tarification LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTarification($tranche1, $tranche2, $tranche3, $tva) {
        $stmt = $this->db->prepare("
            UPDATE tarification 
            SET tranche1 = :tranche1, tranche2 = :tranche2, tranche3 = :tranche3, tva = :tva, updated_at = NOW()
        ");
        return $stmt->execute([
            'tranche1' => $tranche1,
            'tranche2' => $tranche2,
            'tranche3' => $tranche3,
            'tva' => $tva
        ]);
    }

    public function setDefaultTarification() {
        $stmt = $this->db->prepare("
            INSERT INTO tarification (tranche1, tranche2, tranche3, tva) 
            VALUES (0.82, 0.92, 1.1, 18)
            ON DUPLICATE KEY UPDATE 
                tranche1 = VALUES(tranche1), 
                tranche2 = VALUES(tranche2), 
                tranche3 = VALUES(tranche3), 
                tva = VALUES(tva)
        ");
        $stmt->execute();
    }
}
