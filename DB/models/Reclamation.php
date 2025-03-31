<?php
require_once dirname(__DIR__) . "/db.php";

class Reclamation {
    public static function getReclamationsByClientId($client_id) {
        $conn = DB::getConnection();
        $sql = "SELECT * FROM reclamations WHERE client_id = ? ORDER BY date_creation DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$client_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addReclamation($client_id, $type, $description, $statut, $pieces_jointes) {
        $conn = DB::getConnection();
        $sql = "INSERT INTO reclamations (client_id, type, description, statut, pieces_jointes) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$client_id, $type, $description, $statut, $pieces_jointes]);
    }
}
?>
