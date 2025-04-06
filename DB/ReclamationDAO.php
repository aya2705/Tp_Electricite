<?php
require_once "connexion.php";

class ReclamationDAO {

    private $db;

    // Constructeur pour la connexion à la base de données
    public function __construct($db) {
        $this->db = $db;
    }

    public static function getReclamationsByClientId($client_id) { // client_id par défaut à 1
        $conn = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM reclamations WHERE client_id = ? ORDER BY date_creation DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$client_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addReclamation($client_id, $type, $description, $statut) { 
        $conn = Database::getInstance()->getConnection(); // Obtenir la connexion à la base de données
        $sql = "INSERT INTO reclamations (client_id, type, description, statut) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
    
        if ($stmt->execute([$client_id, $type, $description, $statut])) {
            return $conn->lastInsertId(); // Retourner l'ID de la dernière insertion
        }
    
        return false; // Retourner false en cas d'échec
    }

    public static function addPieceJointe($reclamation_id, $fileName, $type) {
        $conn = Database::getInstance()->getConnection();
    
        // Vérifier si la réclamation existe (évite l'erreur de clé étrangère)
        $checkSql = "SELECT COUNT(*) FROM reclamations WHERE reclamation_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$reclamation_id]);
        $exists = $checkStmt->fetchColumn();
    
        if ($exists == 0) {
            throw new Exception("Réclamation ID invalide: $reclamation_id");
        }
    
        // Insérer la pièce jointe
        $sql = "INSERT INTO pieces_jointes (reclamation_id, file_path, type) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
    
        if (!$stmt->execute([$reclamation_id, $fileName, $type])) {
            throw new Exception("Échec d'insertion de la pièce jointe pour la réclamation ID: $reclamation_id");
        }
    
        return true;
    }

    public static function getAllReclamations() {
        $conn = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM reclamations ORDER BY date_creation DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer un client par son ID
    public static function getClientById($clientId ) { 
        $conn = Database::getInstance()->getConnection(); 
        // Correction de la clause JOIN
        $query = "SELECT c.*, u.email 
                  FROM clients c
                  LEFT JOIN users u ON c.user_id = u.user_id
                  WHERE c.client_id = :clientId";             
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer une réclamation par son ID
    public static function getReclamationById($reclamationId) {
        $conn = Database::getInstance()->getConnection();
    
        // Requête pour récupérer la réclamation avec ses pièces jointes
        $query = "SELECT r.*, pj.piece_id, pj.file_path, pj.type 
                  FROM reclamations r
                  LEFT JOIN pieces_jointes pj ON r.reclamation_id = pj.reclamation_id
                  WHERE r.reclamation_id = :reclamationId";
    
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':reclamationId', $reclamationId, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (!$result) {
            return null; // Retourner null si aucune réclamation n'est trouvée
        }
    
        // Organiser les résultats dans un tableau structuré
        $reclamation = [
            'reclamation_id' => $result[0]['reclamation_id'],
            'client_id' => $result[0]['client_id'],
            'type' => $result[0]['type'],
            'description' => $result[0]['description'],
            'statut' => $result[0]['statut'],
            'date_creation' => $result[0]['date_creation'],
            'date_resolution' => $result[0]['date_resolution'],
            'pieces_jointes' => []
        ];
    
        foreach ($result as $row) {
            if ($row['piece_id']) { // Vérifier si une pièce jointe existe
                $reclamation['pieces_jointes'][] = [
                    'piece_id' => $row['piece_id'],
                    'file_path' => $row['file_path'],
                    'type' => $row['type']
                ];
            }
        }
    
        return $reclamation;
    }

    /**
     * Enregistrer ou mettre à jour une réponse
     */
    public static function enregistrerReponse($reclamation_id, $contenu, $statut) {
        $conn = Database::getInstance()->getConnection();
        $sql = "INSERT INTO reclamation_notif (reclamation_id, contenu, status) 
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