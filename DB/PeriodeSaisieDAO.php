<?php
require_once 'connexion.php';

class PeriodeSaisieDAO {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getPeriodeSaisie() {
        $stmt = $this->db->prepare("
            SELECT * FROM periode_saisie 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePeriodeSaisie($date_debut, $date_fin, $active) {
        $stmt = $this->db->prepare("
            UPDATE periode_saisie 
            SET date_debut = :date_debut, date_fin = :date_fin, active = :active, updated_at = NOW()
        ");
        return $stmt->execute([
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'active' => $active
        ]);
    }
}
?>
