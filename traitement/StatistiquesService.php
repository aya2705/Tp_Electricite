<?php
require_once __DIR__ . '/../DB/connexion.php';

class StatistiquesService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Nombre de clients éligibles (tous les clients insérés)
    public function getEligibleClientsCount(): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS count FROM clients");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['count'] : 0;
    }

    // Nombre de saisies effectuées pour le mois et l'année fournis
    public function getSaisiesEffectueesCount(int $month, int $year): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count 
            FROM consommations_mensuelles 
            WHERE MONTH(created_at) = :month 
              AND YEAR(created_at) = :year
        ");
        $stmt->execute(['month' => $month, 'year' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['count'] : 0;
    }

    // Nombre d'anomalies détectées pour le mois et l'année fournis
    public function getAnomaliesCount(int $month, int $year): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count 
            FROM anomalies_consommation 
            WHERE MONTH(entry_date) = :month 
              AND YEAR(entry_date) = :year
        ");
        $stmt->execute(['month' => $month, 'year' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['count'] : 0;
    }

    // Nombre de clients en retard pour le mois et l'année fournis
    // Clients en retard = clients éligibles - clients ayant effectué une saisie ce mois.
    public function getClientsEnRetardCount(int $month, int $year): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT client_id) AS count 
            FROM consommations_mensuelles 
            WHERE MONTH(created_at) = :month 
              AND YEAR(created_at) = :year
        ");
        $stmt->execute(['month' => $month, 'year' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $saisiesClients = $row ? (int)$row['count'] : 0;

        $eligible = $this->getEligibleClientsCount();
        $retard = $eligible - $saisiesClients;
        return ($retard > 0) ? $retard : 0;
    }

    // Somme totale facturée (en MAD) pour le mois et l'année fournis
    public function getFacturesAmountForMonth(int $month, int $year): float {
        $stmt = $this->db->prepare("
            SELECT SUM(montant) AS total 
            FROM factures_mensuelle 
            WHERE MONTH(date_emission) = :month 
              AND YEAR(date_emission) = :year
        ");
        $stmt->execute(['month' => $month, 'year' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float)$row['total'] : 0;
    }

    // Somme totale de kWh consommés pour le mois et l'année donnés
    public function getConsumedKwhForMonth(int $month, int $year): float {
        $stmt = $this->db->prepare("
            SELECT SUM(kw) AS totalKwh 
            FROM consommations_mensuelles 
            WHERE MONTH(created_at) = :month 
              AND YEAR(created_at) = :year
        ");
        $stmt->execute(['month' => $month, 'year' => $year]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float)$row['totalKwh'] : 0;
    }

    // Nombre de réclamations en attente (statut 'en_attente')
    public function getPendingClaimsCount(): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count 
            FROM reclamations 
            WHERE statut = 'en_attente'
        ");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['count'] : 0;
    }
}
?>
