<?php
require_once "connexion.php";
require_once __DIR__ . '/../models/reclamation.php';
require_once __DIR__ . '/../models/piecesJointes.php';

class ReclamationDAO {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public static function getReclamationsByClientId($client_id) { // client_id par défaut à 1
        $conn = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM reclamations WHERE client_id = ? ORDER BY date_creation DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$client_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     /**
     * Ajouter une nouvelle réclamation
     */
    public function addReclamation($client_id, $type, $description, $statut) {
        try {
            $sql = "INSERT INTO reclamations (client_id, type, description, statut) VALUES (:client_id, :type, :description, :statut)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'client_id' => $client_id,
                'type' => $type,
                'description' => $description,
                'statut' => $statut
            ]);
            
            return $this->db->lastInsertId(); // Retourner l'ID de la réclamation insérée
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout d'une réclamation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ajouter une pièce jointe à une réclamation
     */
    public function addPieceJointe($reclamation_id, $fileName, $type) {
        try {
            // Vérifier si la réclamation existe avant d'ajouter une pièce jointe
            if (!self::reclamationExists($reclamation_id)) {
                throw new Exception("Réclamation ID invalide: $reclamation_id");
            }

            $sql = "INSERT INTO pieces_jointes (reclamation_id, file_path, type) VALUES (:reclamation_id, :file_path, :type)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'reclamation_id' => $reclamation_id,
                'file_path' => $fileName,
                'type' => $type
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Erreur d'insertion de pièce jointe: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si une réclamation existe
     */
    private function reclamationExists($reclamation_id) {
        $sql = "SELECT COUNT(*) FROM reclamations WHERE reclamation_id = :reclamation_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['reclamation_id' => $reclamation_id]);
        return $stmt->fetchColumn() > 0;
    }

    public static function getAllReclamations() {
        $conn = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM reclamations ORDER BY date_creation DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer un client par son ID
    public function getClientById($clientId) {
        // Correction de la clause JOIN
        $query = "SELECT c.*, u.email 
                  FROM clients c
                  LEFT JOIN users u ON c.user_id = u.user_id
                  WHERE c.client_id = :clientId";             
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer une réclamation par son ID
    public function getReclamationById($reclamationId) {
        // Requête pour récupérer la réclamation avec ses pièces jointes
        $query = "SELECT r.*, pj.piece_id, pj.file_path, pj.type 
                  FROM reclamations r
                  LEFT JOIN pieces_jointes pj ON r.reclamation_id = pj.reclamation_id
                  WHERE r.reclamation_id = :reclamationId";
    
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':reclamationId', $reclamationId, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (!$result) {
            return null; // Retourner null si aucune réclamation n'est trouvée
        }
    
        // Organiser les résultats dans un tableau structuré
        $reclamation = new Reclamation(
            $result[0]['reclamation_id'], 
            $result[0]['client_id'], 
            $result[0]['type'], 
            $result[0]['description'], 
            $result[0]['statut'], 
            $result[0]['date_creation'], 
            $result[0]['date_resolution']
        );
    
        // Récupérer les pièces jointes
        $piecesJointes = [];
        foreach ($result as $row) {
            if ($row['piece_id']) {
                $piecesJointes[] = $row['file_path']; // Ajouter le chemin du fichier
            }
        }
    
        // Ajouter les pièces jointes à l'objet réclamation
        $reclamation->setPiecesJointes($piecesJointes); // Méthode pour définir les pièces jointes
    
        return $reclamation;
    }
    


    /**
     * Enregistrer ou mettre à jour une réponse
     */
    public static function enregistrerReponse($reclamation_id, $contenu, $statut) {
        $conn = Database::getInstance()->getConnection();
        $sql = "INSERT INTO reponses (reclamation_id, contenu, status) 
                VALUES (:reclamation_id, :contenu, :status)
                ON DUPLICATE KEY UPDATE contenu = :contenu, status = :status, date_reponse = CURRENT_TIMESTAMP";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            ':reclamation_id' => $reclamation_id,
            ':contenu' => $contenu,
            ':status' => $statut
        ]);
    }

    /**
     * Mettre à jour le statut d'une réclamation
     */
    public static function mettreAJourStatutReclamation($reclamation_id, $statut) {
        $conn = Database::getInstance()->getConnection();
        $sql = "UPDATE reclamations 
                SET statut = :statut, 
                    date_resolution = (CASE WHEN :statut = 'résolue' THEN NOW() ELSE date_resolution END) 
                WHERE reclamation_id = :reclamation_id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            ':statut' => $statut,
            ':reclamation_id' => $reclamation_id
        ]);
    }

    /**
     * Traiter une réponse et mettre à jour la réclamation
     */
    public static function traiterReponse($reclamation_id, $response_text, $claim_status) {
        if ($reclamation_id > 0 && !empty($response_text)) {
            try {
                $conn = Database::getInstance()->getConnection();
                $conn->beginTransaction();
                
                // Enregistrer la réponse
                if (!self::enregistrerReponse($reclamation_id, $response_text, $claim_status)) {
                    throw new Exception("Erreur lors de l'enregistrement de la réponse.");
                }
                
                // Mettre à jour le statut de la réclamation
                if (!self::mettreAJourStatutReclamation($reclamation_id, $claim_status)) {
                    throw new Exception("Erreur lors de la mise à jour du statut.");
                }
                
                $conn->commit();
                return true;
            } catch (Exception $e) {
                $conn->rollBack();
                error_log("Erreur: " . $e->getMessage());
                return false;
            }
        }
        return false;
    }
}

?>